<template>
    <div>
        <h2><span class="text-capitalize">Создание тарифа</span></h2>
        <hr>

            <div class="form-group">
                <label for="name">Наименование</label>
                <input type="text" class="form-control col-md-6" name="name" id="name" v-model="name">
            </div>
            <div class="form-group">
                <label for="alias">Алиас</label>
                <input type="text" class="form-control col-md-6" name="alias" id="alias" v-model="alias">
            </div>
            <input type="submit" class="btn btn-success" value="Сохранить" @click="save()">

    </div>
</template>

<script>
export default {
    name: "TariffCreate",
    data() {
        return {
            name: null,
            alias: null
        }
    },
    methods: {
        save() {
            axios.post('create', {name: this.name, alias: this.alias}).catch((response) => {
                console.log(response)
            }).then((response) => {
                if (response.status === 200) {
                    window.location = response.data
                }
            })
        }
    }
}
</script>

<style scoped>

</style>