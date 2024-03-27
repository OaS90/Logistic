<template>
    <div class="row">
        <div class="form-group col-md-3">
            <label for="name">Наименование</label>
            <input type="text" class="form-control" id="name" v-model="tariff.name" :disabled="!isEnabled">
        </div>
        <div class="form-group col-md-3">
            <label for="alias">Алиас</label>
            <input type="text" class="form-control" id="alias" v-model="tariff.alias" :disabled="!isEnabled">
        </div>
        <div class="form-group col-md-3 mt-4 pt-2" v-if="userId === tariffAuthor || isAdmin || isEditable">
            <button class="btn btn-success" @click="updateNameOrAlias">Обновить имя или алиас</button>
        </div>
    </div>
</template>

<script>
export default {
    name: "TariffNameAliasForm",
    props: ['tariff', 'userId', 'tariffAuthor', 'isAdmin', 'isEditable'],
    data() {
        return {

        }
    },
    methods: {
        updateNameOrAlias() {
            axios.post('update-name-or-alias', {
                name: this.tariff.name,
                alias: this.tariff.alias
            }).then((response) => {
                console.log(response.data.message)
            }).catch((error) => {
                console.log(error.response.data.message)
            })
        }
    },
    computed: {
        isEnabled() {
            return this.userId === this.tariffAuthor || this.isAdmin || this.isEditable
        }
    }
}
</script>

<style scoped>

</style>