<script>
export default {
    name: "SettingsTabs",
    model: {
        prop: 'modelValue',
        event: 'change'
    },
    props: {
        variant: {
            type: String,
            default: 'default'//'default' | 'underline' | 'pills'
        },
        directive: {
            type: String,
            default: 'if'//"if" | "show"
        },
    }
}
</script>

<template>
    <fwb-tabs v-model="modelValue" :variant="variant" :directive="directive">
        <slot/>
    </fwb-tabs>
</template>

<style scoped>

</style>
