<template>
    <div class="modal" :class="{active: show}">
        <slot></slot>
    </div>
</template>

<script>
export default {
    name: "Popup",
    props: ['show'],
}
</script>

<style scoped>

</style>
