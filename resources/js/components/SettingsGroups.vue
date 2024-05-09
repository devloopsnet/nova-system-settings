<script>
export default {
    name: "SettingsGroups",
    props: {
        name: String,
        flush: {
            type: Boolean,
            default: false
        },
        alwaysOpen: {
            type: Boolean,
            default: true
        },
        openFirstItem: {
            type: Boolean,
            default: false
        },
    }
}
</script>

<template>
    <fwb-accordion :data-settings-group-name="name" :always-open="alwaysOpen" :flush="flush" :open-first-item="openFirstItem" class="w-full">
        <slot/>
    </fwb-accordion>
</template>

<style scoped>

</style>
