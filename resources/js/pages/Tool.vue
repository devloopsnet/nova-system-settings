<template>
    <div id="nova-system-settings">
        <Head :title="__('System Settings')"/>
        <Heading class="mb-6">{{ __('System Settings') }}</Heading>
        <Card class="!rounded-none !rounded-t-lg dark:!bg-gray-900">
            <div class="flex flex-col pb-0.5">

                <SettingsTabs class="py-5 px-2" v-model="systemSettings.activeGroup" variant="underline">
                    <fwb-tab v-for="group in systemSettings.groups" :title="group.groupTitle" :name="group.groupName">
                        <div class="md:flex">
                            <ul class="flex-column w-96 space-y space-y-4 text-sm font-medium text-gray-500 dark:text-gray-400 md:me-4 mb-4 md:mb-0">
                                <li v-for="settingsMenus in group.settings" :class="`${settingsMenus.activeTab ? 'active' : ''}`">
                                    <a
                                        @click="toggleSettingsTabs(settingsMenus, group.settings)"
                                        :class="`${!settingsMenus.activeTab ? 'hover:dark:bg-gray-700 hover:dark:text-gray-100 hover:bg-gray-200 hove:text-gray-400' : ''} cursor-pointer inline-flex items-center px-4 py-3 bg-gray-100 dark:bg-gray-800 active:bg-primary-500 font-bold rounded-lg w-full text-gray-500 active:text-white dark:text-white`" aria-current="page">
                                        <component :is="`heroicons-solid-${settingsMenus.icon}`" class="w-6 h-6 me-2 text-gray-500 dark:text-white active:text-white"/>
                                        {{ settingsMenus.title }}
                                    </a>
                                </li>
                            </ul>
                            <div class="flex flex-col w-full min-h-96">
                                <div v-for="settings in group.settings">
                                    <div v-if="settings.activeTab" class="w-full h-full p-6 rounded-lg border border-gray-200 shadow">
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ settings.title }}</h3>
                                        <ht class="py-2"/>
                                        <LoadingView :loading="loading">
                                            <form
                                                class="space-y-8"
                                                @submit="evt => saveSettings(evt, group, settings)"
                                                :data-form-unique-id="settings.name"
                                                autocomplete="off"
                                                :ref="`${settings.name}`">
                                                <div class="relative overflow-hidden divide-y divide-gray-100 dark:divide-gray-700">
                                                    <KeepAlive>
                                                        <template v-for="(field, index) in settings.fields" :key="'tab-' + index">
                                                            <component
                                                                v-if="!field.from"
                                                                :is="getComponentName(field)"
                                                                ref="fields"
                                                                :class="{'remove-bottom-border': index === settings.fields.length - 1,}"
                                                                :errors="validationErrors"
                                                                :field="field"
                                                                :form-unique-id="formUniqueId"
                                                                :related-resource-id="relatedResourceId"
                                                                :related-resource-name="relatedResourceName"
                                                                :resource-id="resourceId"
                                                                :resource-name="resourceName"
                                                                :show-help-text="field.helpText != null"
                                                                :shown-via-new-relation-modal="shownViaNewRelationModal"
                                                                :via-relationship="viaRelationship"
                                                                :via-resource="viaResource"
                                                                :via-resource-id="viaResourceId"
                                                                @field-changed="$emit('field-changed')"
                                                                @file-deleted="$emit('update-last-retrieved-at-timestamp')"
                                                                @file-upload-started="$emit('file-upload-started')"
                                                                @file-upload-finished="$emit('file-upload-finished')"
                                                            />
                                                            <component
                                                                v-if="field.from"
                                                                :is="getComponentName(field)"
                                                                :errors="validationErrors"
                                                                :resource-id="getResourceId(field)"
                                                                :resource-name="field.resourceName || resourceName"
                                                                :field="field"
                                                                :via-resource="field.from.viaResource"
                                                                :via-resource-id="field.from.viaResourceId"
                                                                :via-relationship="field.from.viaRelationship"
                                                                :form-unique-id="relationFormUniqueId"
                                                                @field-changed="$emit('field-changed')"
                                                                @file-deleted="$emit('update-last-retrieved-at-timestamp')"
                                                                @file-upload-started="$emit('file-upload-started')"
                                                                @file-upload-finished="$emit('file-upload-finished')"
                                                                :show-help-text="field.helpText != null"
                                                            />
                                                        </template>
                                                    </KeepAlive>
                                                </div>
                                                <div class="flex flex-col md:flex-row md:items-center justify-center md:justify-end space-y-2 md:space-y-0 md:space-x-3">
                                                    <Button
                                                        type="submit"
                                                        dusk="confirm-preview-button"
                                                        @click="evt => saveSettings(evt, group, settings)"
                                                        :label="__('Save :settings',{'settings':settings.title})"
                                                        :disabled="isWorking"
                                                        :loading="savingSettings"
                                                    />
                                                </div>
                                            </form>
                                        </LoadingView>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fwb-tab>
                </SettingsTabs>
            </div>
        </Card>
    </div>
</template>

<script>
import each from 'lodash/each'
import tap from 'lodash/tap'
import {mapActions, mapMutations} from 'vuex'

let HandlesFormRequest = require('../../../../../vendor/laravel/nova/resources/js/mixins/HandlesFormRequest').default;
let HandlesUploads = require('../../../../../vendor/laravel/nova/resources/js/mixins/HandlesUploads').default;
let InteractsWithResourceInformation = require('../../../../../vendor/laravel/nova/resources/js/mixins/InteractsWithResourceInformation').default;

import {Button} from 'laravel-nova-ui'

export default {
    components: {
        Button
    },
    mixins: [
        HandlesFormRequest,
        HandlesUploads,
        InteractsWithResourceInformation,
    ],
    data() {
        return {
            isWorking: false,
            savingSettings: false,
            loading: false,
            activeTab: '',
            tabMode: 'form',
            systemSettings: Nova.config('systemSettings')
        };
    },
    mounted() {
        let settings = this.systemSettings.groups[this.systemSettings.activeGroup].settings;
        for (let settingsName in settings) {
            if (settings[settingsName].activeTab) {
                this.activeTab = settings[settingsName].name;
                return
            }
        }
    },
    methods: {
        ...mapMutations([
            'allowLeavingForm',
            'preventLeavingForm',
        ]),
        /**
         * Toggle settings tab.
         *
         * @param settings
         * @param allSettings
         */
        toggleSettingsTabs(settings, allSettings) {
            for (let settingsName in allSettings) {
                allSettings[settingsName].activeTab = false;
            }
            this.activeTab = settings.name;
            settings.activeTab = true;
        },

        /**
         * Reload all settings fields.
         *
         */
        async reloadFields() {
            this.loading = true;
            const {
                data: {systemSettings},
            } = await Nova.request().get(
                `/nova-vendor/system-settings/load-settings`,
                {
                    params: {
                        activeGroup: this.systemSettings.activeGroup,
                        activeTab: this.activeTab
                    },
                }
            )

            this.systemSettings = systemSettings;
            this.loading = false;
        },

        /**
         * Save settings.
         *
         * @param e
         * @param group
         * @param settings
         * @returns {Promise<void>}
         */
        async saveSettings(e, group, settings) {
            e.preventDefault();
            await this.doSaveSettings(group, settings)
        },

        /**
         * Perform saving the settings settings.
         *
         * @param group
         * @param settings
         * @returns {Promise<void>}
         */
        async doSaveSettings(group, settings) {
            this.isWorking = true

            if (this.$refs[settings.name][0].reportValidity()) {
                try {
                    const {data: {status, error}} = await this.createSettingsRequest(group, settings)

                    this.allowLeavingForm();

                    if (status) {
                        Nova.success(
                            this.__('The :settings saved successfully!', {
                                settings: settings.title,
                            })
                        )
                    } else {
                        Nova.error(error);
                    }

                    window.scrollTo(0, 0)
                    // Reset the form by refetching the fields
                    this.reloadFields()
                    this.resetErrors()
                    this.isWorking = false
                    return
                } catch (error) {
                    window.scrollTo(0, 0)
                    this.isWorking = false
                    this.loading = false;
                    this.preventLeavingForm();
                    this.handleResponseError(error)
                }
            }
            this.isWorking = false
        },

        /**
         * Send a save request for selected settings.
         *
         * @param group
         * @param settings
         * @returns {*}
         */
        createSettingsRequest(group, settings) {
            return Nova.request().post(
                `/nova-vendor/system-settings/save-settings`,
                this.createSettingsFormData(group, settings),
            )
        },

        /**
         * Create the form data for saving the selected settings.
         *
         * @param group
         * @param settings
         * @returns {FormData}
         */
        createSettingsFormData(group, settings) {
            return tap(new FormData(), formData => {
                each(settings.fields, field => {
                    field.fill(formData);
                })

                formData.append('settingsName', settings.name);
                formData.append('groupName', group.groupName);
            })
        },

        /**
         * Get the component name.
         *
         * @param field
         * @returns {string}
         */
        getComponentName(field) {
            return field.prefixComponent
                ? this.tabMode + '-' + field.component
                : field.component
        },
    }
}
</script>

<style>
#nova-system-settings {
    :is(.active\:bg-primary-500:is(.active *)) {
        background-color: rgba(var(--colors-primary-500));;
    }

    :is(.active\:text-white:is(.active *)) {
        color: white;
    }
}

/* we will explain what these classes do next! */
.v-enter-active,
.v-leave-active {
    transition: opacity 0.3s ease;
}

.v-enter-from,
.v-leave-to {
    opacity: 0;
}
</style>
