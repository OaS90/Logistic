<template>
    <div>
        <label for="file-upload" class="btn btn-primary" data-modal="modal-request1" style="margin-bottom: 0; margin-right: 5px" id="button">
            <span>Импорт c выводом изменений</span>
        </label>
        <input type="file" hidden id="file-upload" @change="beforeImport" ref="fileUpload">
        <button class="btn btn-primary"  @click="exportSettings">Экспорт</button>
        <a target="_blank" href="https://yandex.ru/map-constructor/" class="btn btn-success ml-1">Конструктор карт</a>
        <a @click="showHistory" class="btn btn-secondary ml-1">История выгрузок</a>
        <a v-if="hasEmptyPrices" href="/admin/tariffs/validation" class="btn btn-danger ml-1">Незаполненные цены для категорий</a>
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
                    <th style="width: 15%">Id полигона в сервисе</th>
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
                    <td>{{ zone.id }}</td>
                    <td></td>
                    <td>{{ zone.region_id }}</td>
                    <td>{{ zone.type }}</td>
                    <td>{{ zone.zone_name }}</td>
                    <td>{{ zone.region_name }}</td>
                    <td>{{ zone.filial_code }}</td>
                </tr>
                <tr v-for="(zone, index) in deletedPolygons" class="deleted-zones">
                    <td class="choice">
<!--                        <input type="checkbox" class="input-lg checkbox" v-model="zone.to_import">-->
                    </td>
                    <td>{{ zone.id }}</td>
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
                    <td></td>
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
                                <template v-slot:singleLabel="{ option }">Id региона: {{ option.region_id }}</template>
                            </multiselect>
                        </td>

                    <td>
                        <select class="form-control" v-model="newZone.type">
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

        <modal v-if="showHistoryModal" @close="showHistoryModal = false">
            <template #body>
                <div style="max-height: 340px; overflow-y: auto">
                    <ul style="list-style: none; overflow-y: auto">
                        <li v-for="file in exportsHistory">
                            <a @click="showHistoryModal = false" :href="'/admin/yandex-zones/export/history/' + file + '/download'">{{ file }}</a>
                        </li>
                    </ul>
                </div>
                <br>
                <button class="btn btn-secondary" @click="showHistoryModal = false">ОК</button>
            </template>
        </modal>

        <modal v-if="showModalZonesWithErrors" @close="showModalZonesWithErrors = false">
            <template #body>
                {{ errorText }}
                <br>
                <h3>Зоны загруженные с ошибками</h3>
                <table class="table-bordered">
                    <thead>
                        <tr>
                            <th class="p-2">Регион</th>
                            <th class="p-2">Филиал</th>
                            <th class="p-2">Тип</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="zone in zonesWithErrors">
                            <td class="p-2">{{zone.region_name}}</td>
                            <td class="p-2">{{zone.filial_id}}</td>
                            <td class="p-2">{{zone.type}}</td>
                        </tr>
                    </tbody>
                </table>

                    <br>
                    <button class="btn btn-secondary" @click="showModalZonesWithErrors = false">ОК</button>
            </template>
        </modal>
    </div>
</template>

<script>
import Modal from "./Modal.vue";
import Multiselect from "vue-multiselect";
import {DotLoader, PulseLoader} from "vue3-spinner";
export default {
    props: ['zones', 'regions', 'polygonTypes', 'filials', 'exportsHistory'],
    data() {
        return {
            showModalZonesWithErrors: false,
            zonesWithErrors: [],
            changedZones: null,
            newPolygons: null,
            showModal: false,
            loading: false,
            errorText: '',
            newPolygonsData: [],
            deletedPolygons: [],
            newPolygonsErrors: [],
            showHistoryModal: false,
            hasEmptyPrices: false
        }
    },
    components: {
        PulseLoader,
        Modal,
        DotLoader,
        Multiselect
    },
    methods: {
        showHistory() {
            this.showHistoryModal = !this.showHistoryModal
        },
        disableZoneInput(zone) {
            if (zone.type === 'allow-zones' || zone.type === 'polygon-paths') {
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
                .catch(errors => {
                    if (errors.response.status > 400) {
                        this.loading = false

                        if (errors.data.message) {
                            this.errorText = errors.data.message
                        } else {
                            this.errorText = 'Ошибка обработки данных.'
                        }
                    }
                })
                .then(response => {
                    if (response.status < 300) {
                        this.changedZones = response.data.changes
                        this.newPolygons = response.data.new_polygons
                        this.deletedPolygons = response.data.deleted_polygons
                        for (let i = 0; i < this.newPolygons.length; i++) {
                            this.newPolygonsData[i] = {
                                region: {name: null},
                                type: null,
                                zone: null,
                                filial: null,
                                polygon_data: this.newPolygons[i],
                                to_import: false
                            }
                        }
                    }
                })

            this.$refs.fileUpload.value = null;
        },
        exportSettings() {
            this.loading = true;
            this.showModal = true

            axios.get('/admin/yandex-zones/export', {responseType: 'blob'})
                .catch(errors => {
                    if (errors.response.status > 400) {
                        this.loading = false
                        this.errorText = 'Ошибка экспорта из сервиса.'
                    }
                })
                .then(response => {
                    if (response.status < 300) {
                        this.loading = false
                        this.showModal = false
                        const url = window.URL.createObjectURL(new Blob([response.data]));
                        const link = document.createElement('a');
                        link.href = url;
                        link.setAttribute('download', 'data.geojson');
                        document.body.appendChild(link);
                        link.click();
                    }
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

            zonesToImport.deleted = this.deletedPolygons.map(polygon => {
                polygon.to_import = true

                return polygon
            })

            zonesToImport.new.forEach((item, index) => {
                if (!item.region.id || !item.filial || !item.type) {
                    this.newPolygonsErrors.push(index)
                } else if (!item.zone && item.type === 'delivery-zones') {
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
            }).catch(errors => {
                if (errors.response.status > 400) {
                    this.loading = false
                    this.errorText = 'Ошибка импорта в сервис.'
                }
            }).then(response => {
                this.loading = false
                this.changedZones = null
                this.newPolygons = null

                if (response.status < 300) {
                    this.errorText = response.data.message

                    if (response.data.result.zones_errors.length > 0) {
                        this.showModalZonesWithErrors = true
                        this.zonesWithErrors = response.data.result.zones_errors
                    }

                    if (response.data.result.has_empty_prices) {
                        this.hasEmptyPrices = true
                    }
                }
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
