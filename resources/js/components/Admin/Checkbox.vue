<template>
    <div>
        <input class="input checkbox" type="checkbox" @change="updateField" v-model="isActive">
    </div>
</template>

<script>
export default {
    props: ['entry', 'uri', 'type'],
    data() {
        return {
            isActive: false
        }
    },
    beforeMount() {
        if (this.type === 'main_quotes') {
            this.isActive = this.entry.is_active_for_quotes
        } else {
            this.isActive = this.entry.is_active_for_tk
        }
    },
    methods: {
        updateField() {
            axios.post('/admin/filials/' + this.entry.id + '/' + this.uri, {is_active: this.isActive})
                .then(response => {
                    console.log('OK')
                }).catch(error => {
                    alert('Ошибка обновления!')
            })
        }
    },
}
</script>

<style scoped>

</style>
