<template>
    <div>
        <label for="file-upload" class="btn btn-primary" data-modal="modal-request1" style="margin-bottom: 0; margin-right: 5px" id="button">
            <span>Импорт c выводом изменений</span>
        </label>
        <input type="file" hidden id="file-upload" @change="beforeImport" ref="fileUpload">
        <button class="btn btn-primary"  @click="exportSettings">Экспорт</button>
        <a target="_blank" href="https://yandex.ru/map-constructor/" class="btn btn-success ml-1">Конструктор карт</a>

        <hr>

        <div v-if="changedZones">
            <h2>Изменения в зонах</h2>

            <button class="btn btn-primary mb-2" @click="importSettings">Отправить в сервис</button>
            <p>
                <span class="changed-zones">Изменено: {{ changedZones.length }}</span>
                <span class="new-zones">Новые: {{ newPolygons.length }}</span>
                <span class="deleted-zones">Удалённые: {{ deletedPolygons.length }}</span>
            </p>

            <table class="table table-bordered w-100">
                <thead>
                <tr>
                    <th class="choice" style="width: 5%"><input type="checkbox" class="input-lg checkbox" @change="selectAllZones($event)"></th>
                    <th>Описание</th>
                    <th style="width: 15%">Id Региона</th>
                    <th style="width: 15%">Тип полигона</th>
                    <th style="width: 15%">Зона</th>
                    <th style="width: 15%">Регион</th>
                    <th style="width: 15%">Код филала</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(zone, index) in changedZones" class="changed-zones">
                    <td class="choice">
                        <input type="checkbox" class="input-lg checkbox" v-model="zone.to_import">
                    </td>
                    <td></td>
                    <td>{{ zone.region_id }}</td>
                    <td>{{ zone.type }}</td>
                    <td>{{ zone.zone_name }}</td>
                    <td>{{ zone.region_name }}</td>
                    <td>{{ zone.filial_code }}</td>
                </tr>
                <tr v-for="(zone, index) in deletedPolygons" class="deleted-zones">
                    <td class="choice">
                        <input type="checkbox" class="input-lg checkbox" v-model="zone.to_import">
                    </td>
                    <td></td>
                    <td>{{ zone.region_id }}</td>
                    <td>{{ zone.type }}</td>
                    <td>{{ zone.zone_name }}</td>
                    <td>{{ zone.region_name }}</td>
                    <td>{{ zone.filial_code }}</td>
                </tr>
                <tr v-for="(newZone, newPolygonIndex) in newPolygonsData" class="new-zones">
                    <td class="choice">
                        <input type="checkbox" class="input-lg checkbox" v-model="newZone.to_import">
                    </td>
                    <td>{{ newZone.polygon_data.properties.description }}</td>
                        <td>
                            <multiselect :options="regions"
                                         placeholder="Выберите регион"
                                         v-model="newZone.region"
                                         :multiple="false"
                                         track-by="name"
                                         label="name"
                                         :selectLabel="''"
                            >
                                <template v-slot:singleLabel="{ option }">Id региона: {{ option.id }}</template>
                            </multiselect>
                        </td>

                    <td>
                        <select class="form-control" v-model="newZone.polygon_type">
                            <option :value="polygon"
                                    v-for="(polygon, index) in polygonTypes"
                            >
                                {{ polygon }}
                            </option>
                        </select>
                    </td>
                    <td>
                        <select v-model="newZone.zone" class="form-control"
                                :disabled="disableZoneInput(newZone)"
                        >
                            <option :value="zoneCode" v-for="(zoneCode, index) in zones">{{ zoneCode }}</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" class="form-control" disabled v-model="newZone.region.name">
                    </td>
                    <td>
                        <multiselect :options="filials"
                                     placeholder="Выберите регион"
                                     v-model="newZone.filial"
                                     :multiple="false"
                                     track-by="name"
                                     :selectLabel="''"
                                     label="name"
                        >
                            <template v-slot:singleLabel="{ option }">Id филиала: {{ option.filial_id }}</template>
                        </multiselect>
                    </td>
                </tr>
                </tbody>

            </table>
        </div>

        <modal v-if="showModal" @close="showModal = false">
            <template #body>
                <template v-if="loading">
                    <PulseLoader :loading="loading" :color="'#7c69ef'"/>
                </template>
                <template v-else>
                    {{ errorText }}
                    <br>
                    <button class="btn btn-secondary" @click="showModal = false">ОК</button>
                </template>
            </template>
        </modal>
    </div>
</template>

<script>
import Modal from "./Modal.vue";
import Multiselect from "vue-multiselect";
import {DotLoader, PulseLoader} from "vue3-spinner";
export default {
    props: ['zones', 'regions', 'polygonTypes', 'filials'],
    data() {
        return {
            changedZones: null,
            newPolygons: null,
            showModal: false,
            loading: false,
            errorText: '',
            newPolygonsData: [],
            deletedPolygons: [],
            newPolygonsErrors: []
        }
    },
    components: {
        PulseLoader,
        Modal,
        DotLoader,
        Multiselect
    },
    methods: {
        disableZoneInput(zone) {
            if (zone.polygon_type === 'allow-zones' || zone.polygon_type === 'polygon-paths') {
                zone.zone = null
                return true
            }
        },
        selectAllZones(event) {
            this.changedZones.forEach(zone => {
                zone.to_import = !!event.target.checked
            })

            this.newPolygonsData.forEach(zone => {
                zone.to_import = !!event.target.checked
            })

            this.deletedPolygons.forEach(zone => {
                zone.to_import = !!event.target.checked
            })
        },
        beforeImport(event) {
            let formData = new FormData()
            formData.append('document', event.target.files[0])

            axios.post('/admin/yandex-zones/prepare-import', formData)
                .then(response => {
                    this.changedZones = response.data.changes
                    this.newPolygons = response.data.new_polygons
                    this.deletedPolygons = response.data.deleted_polygons

                    for (let i = 0; i <  this.newPolygons.length; i++) {
                        this.newPolygonsData[i] = {
                            region: { name: null },
                            polygon_type: null,
                            zone: null,
                            filial: null,
                            polygon_data: this.newPolygons[i],
                            to_import: false
                        }
                    }
                }).catch(errors => {
                    this.loading = false
                    this.errorText = 'Ошибка обработки данных.'
                })

            this.$refs.fileUpload.value = null;
        },
        exportSettings() {
            this.loading = true;
            this.showModal = true

            axios.get('/admin/yandex-zones/export', {responseType: 'blob'})
                .then(response => {
                    this.loading = false
                    this.showModal = false
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'data.geojson');
                    document.body.appendChild(link);
                    link.click();
                })
                .catch(error => {
                    this.loading = false
                    this.errorText = 'Ошибка экспорта из сервиса.'
                })
        },

        importSettings() {
            this.newPolygonsErrors = []
            let zonesToImport = {}
            let allZones = {}
            allZones.changed = this.changedZones
            allZones.new = this.newPolygonsData
            allZones.deleted = this.deletedPolygons

            zonesToImport.changed = this.changedZones.filter(polygon => {
                return polygon.to_import === true
            })

            zonesToImport.new = this.newPolygonsData.filter(polygon => {
                return polygon.to_import === true
            })

            zonesToImport.deleted = this.deletedPolygons.filter(polygon => {
                return polygon.to_import === true
            })

            zonesToImport.new.forEach((item, index) => {
                if (!item.region || !item.filial || !item.polygon_type) {
                    this.newPolygonsErrors.push(index)
                } else if (!item.zone && item.polygon_type === 'delivery-zones') {
                    this.newPolygonsErrors.push(index + '_polygon-type')
                }
            })

            if (this.newPolygonsErrors.length > 0) {
                this.showModal = true
                this.errorText = 'Заполнены не все поля для новой зоны '
                return null
            }

            this.showModal = true
            this.loading = true

            axios.post('/admin/yandex-zones/import-to-service', {
                zonesToImport: zonesToImport,
                zones: allZones,
            }).then(response => {
                this.showModal = false;
                this.loading = false
                this.changedZones = null
                this.newPolygons = null
            }).catch(errors => {
                this.loading = false
                this.errorText = 'Ошибка импорта в сервис.'
            })
        }
    }
}
</script>

<style scoped>
.choice {
    text-align: center;
    width: 5%;
}
input[type=checkbox] {
    transform: scale(1.5);
}
.changed-zones {
    background-color: lightgoldenrodyellow;
}
.deleted-zones {
    background-color: lightcoral;
}
.new-zones {
    background-color: lightgreen;
}
span.new-zones {
    padding: 5px;
}
span.deleted-zones {
    padding: 5px;
}
span.changed-zones {
    padding: 5px;
}
</style>
