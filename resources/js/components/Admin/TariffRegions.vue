<template>
    <div class="row">
        <div class="form-group col-md-7" v-if="userId === tariffAuthor || isAdmin || isEditable">
            <multiselect :options="regions"
                         placeholder="Выберите регион(ы)"
                         v-model="selected"
                         :multiple="true"
                         track-by="name"
                         label="name"
            >
            </multiselect>

        </div>
        <div class="form-group col-md-2" v-if="userId === tariffAuthor || isAdmin || isEditable">
            <button class="btn btn-default" @click="addRegions">Добавить регион(ы)</button>
        </div>
        <div class="form-group col-md-3" v-if="userId === tariffAuthor || isAdmin || isEditable">
            <button class="btn btn-behance" @click="addAllRegions">Добавить <b>все</b> регионы</button>
        </div>
        <div v-for="(region, index) in tariffRegionData" class="form-group col-md-2 region-column">
            <a :href=regionUrl(region.id)>{{ region.name }}</a>
            <i class="la la-close delete-region"
               v-if="userId === tariffAuthor || isAdmin || isEditable"
               title="Удалить регион"
               @click="setRegionForDeleting(region.id, index)"></i>
        </div>
        <modal v-if="showDeleteRegionModal">
            <template #body>
                {{ modalText }}
                <br>
                <button :class="btnClass" @click="deleteRegion(regionForDeleting.id, regionForDeleting.index)">{{ btnText }}</button>
            </template>
        </modal>
    </div>
</template>

<script>
import Multiselect from "vue-multiselect";
import Modal from "./Modal.vue";

export default {
    name: "TariffRegions",
    props: ['tariffRegions', 'regions', 'tariffId', 'userId', 'tariffAuthor', 'isAdmin', 'isEditable'],
    components: {
        Multiselect,
        Modal
    },
    data() {
        return {
            selected: null,
            showDeleteRegionModal: false,
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
            this.showDeleteRegionModal = true
            this.regionForDeleting = {id: regionId, index: index}
        },
        regionUrl(regionId) {
            return 'edit/regions/' + regionId + '/edit'
        },
        deleteRegion(regionId, index) {
            this.showDeleteRegionModal = !this.showDeleteRegionModal
            this.tariffRegionData.splice(index, 1)

            axios.delete('delete-region/' + regionId, {
                headers: {
                    accept: 'application/json', 'Content-Type': 'application/json'
                }
            }).then((response) => {
                if (response.status === 200) {
                    this.modalText = response.data.message
                }
            }).catch((error) => {
                if (error.response.status === 500) {
                    this.showDeleteRegionModal = !this.showDeleteRegionModal
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
                this.tariffRegionData = regionsData
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
