<template>
    <div class="row">
        <div class="form-group col-md-7">
            <multiselect :options="regions"
                         placeholder="Выберите регион(ы)"
                         v-model="selected"
                         :multiple="true"
                         track-by="name"
                         label="name"
            >
            </multiselect>

        </div>
        <div class="form-group col-md-2">
            <button class="btn btn-default" @click="addRegions">Добавить регион(ы)</button>
        </div>
        <div class="form-group col-md-3">
            <button class="btn btn-behance" @click="addAllRegions">Добавить <b>все</b> регионы</button>
        </div>
        <div v-for="(region, index) in tariffRegionData" class="form-group col-md-2 region-column">
            <a :href=regionUrl(region.id)>{{ region.name }}</a>
            <i class="la la-close delete-region" title="Удалить регион" @click="setRegionForDeleting(region.id, index)"></i>

        </div>
        <modal v-if="showModal">
            <span slot="body">
                {{ modalText }}
                <br>
                <button :class="btnClass" @click="deleteRegion(regionForDeleting.id, regionForDeleting.index)">{{ btnText }}</button>
            </span>
            <span slot="footer"></span>
        </modal>
    </div>
</template>

<script>
import Multiselect from "vue-multiselect";
import Modal from "./Modal";

export default {
    name: "TariffRegions",
    props: ['tariffRegions', 'regions', 'tariffId'],
    components: {
        Multiselect,
        Modal
    },
    data() {
        return {
            selected: null,
            showModal: false,
            modalText: 'Удалить регион?',
            modalBtnName: 'Удалить',
            modelBtnClass: 'btn btn-danger',
            tariffRegionData: null,
            regionForDeleting: {},
            btnText: 'OK',
            btnClass: 'btn btn-secondary'
        }
    },
    mounted() {
        this.tariffRegionData = this.tariffRegions
    },
    methods: {
        setRegionForDeleting(regionId, index) {
            this.showModal = true
            this.regionForDeleting = {id: regionId, index: index}
        },
        regionUrl(regionId) {
            return 'edit/regions/' + regionId + '/edit'
        },
        deleteRegion(regionId, index) {
            this.showModal = !this.showModal
            this.tariffRegionData.splice(index, 1)

            axios.delete('delete-region/' + regionId, {
                headers: {
                    accept: 'application/json', 'Content-Type': 'application/json'
                }
            }).then((response) => {
                if (response.status === 200) {
                    const filteredPeople = people.filter((item) => item.id !== idToRemove);
                    this.showModal = true
                    this.modalText = response.data.message
                }
            }).catch((error) => {
                if (error.response.status === 500) {
                    this.showModal = true
                    this.modalText = 'Ошибка ' + error.response.data.message
                }
            })
        },
        addRegions() {
            let regionsData = this.tariffRegionData

            this.selected.forEach(function (item) {
                let regionExists = false

                this.some(function (region) {
                    if (region.id === item.id) {
                        regionExists = true;
                    }
                })

                if (!regionExists) {
                    this.push(item)

                }
            }, regionsData)

            axios.post('add-regions', this.selected, {
                headers: {
                    accept: 'application/json', 'Content-Type': 'application/json'
                }
            }).then((response) => {
                this.tariffRegionData = response.data.regions
            }).catch((error) => {
                if (error.response.status === 500) {
                    this.showModal = true
                    this.modalText = 'Ошибка ' + error.response.data.message
                }
            })


            this.selected = null
        },
        addAllRegions() {
            axios.post('add-all-regions', null,{
                headers: {
                    accept: 'application/json', 'Content-Type': 'application/json'
                }
            }).then((response) => {
                if (response.status === 200) {
                    this.tariffRegionData = this.regions
                }
            }).catch((error) => {
                if (error.response.status === 500) {
                    this.showModal = true
                    this.modalText = 'Ошибка ' + error.response.data.message
                }
            })
        }
    }
}
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style scoped>
.delete-region:hover {
    cursor: pointer
}
.region-column {
    text-align: center;
}


</style>