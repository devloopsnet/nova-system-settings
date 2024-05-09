<script>
export default {
    name: "SettingsGroup",
    props: {
        title: String
    }
}
</script>

<template>
    <fwb-accordion-panel>
        <fwb-accordion-header>{{ title }}</fwb-accordion-header>
        <fwb-accordion-content>
            <slot/>
        </fwb-accordion-content>
    </fwb-accordion-panel>
</template>

<style scoped>

</style>
