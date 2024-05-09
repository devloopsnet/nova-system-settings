<script>
export default {
    name: "SettingsTab",
    props: {
        name: String,
        title: String,
        fields: Array
    },
    mounted() {
        console.log('this.name => ', this.name);
        console.log('this.title => ', this.title);
        console.log('this.fields => ', this.fields);
    }
}
</script>

<template>

</template>

<style scoped>

</style>
