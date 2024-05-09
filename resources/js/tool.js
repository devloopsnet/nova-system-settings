import '../../node_modules/flowbite-vue/dist/index.css'
import {
    FwbTab,
    FwbTabs,
    FwbAccordion,
    FwbAccordionPanel,
    FwbAccordionHeader,
    FwbAccordionContent,
} from 'flowbite-vue'
import Tool from './pages/Tool'
import SettingsTab from "./components/SettingsTab.vue";
import SettingsTabs from "./components/SettingsTabs.vue";
import SettingsGroup from "./components/SettingsGroup.vue";
import SettingsGroups from "./components/SettingsGroups.vue";

Nova.booting((app, store) => {
    app.component('fwb-tab', FwbTab);
    app.component('fwb-tabs', FwbTabs);
    app.component('fwb-accordion', FwbAccordion);
    app.component('fwb-accordion-panel', FwbAccordionPanel);
    app.component('fwb-accordion-header', FwbAccordionHeader);
    app.component('fwb-accordion-content', FwbAccordionContent);
    app.component('SettingsTab', SettingsTab);
    app.component('SettingsTabs', SettingsTabs);
    app.component('SettingsGroup', SettingsGroup);
    app.component('SettingsGroups', SettingsGroups);
    Nova.inertia('NovaSystemSettings', Tool)
})
