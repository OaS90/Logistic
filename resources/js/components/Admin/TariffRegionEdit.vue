<template>
    <div>
        <h1>Категории доставки товаров</h1>
        <a :href="this.backUrl">
            <i class="la la-angle-double-left"></i>Вернуться к настройкам тарифа
        </a>
        <hr>
        <div v-if="userId === authorId || isAdmin || isEditable">
            <div class="row">
                <div class="col-md-12">
                    <input type="button" class="btn btn-success" value="Сохранить" @click="save">
                </div>
            </div>
            <table id="categories"
                   class="table-content bg-white table table-striped table-hover nowrap rounded shadow-xs border-xs mt-2 dataTable dtr-inline collapsed"
            >
                <thead>
                    <tr>
                        <th>Категория</th>
                        <th>Вкл/Выкл</th>
                        <th v-for="(details, index) in headers" class="th-title">
                            {{ details.title }}
                            <i class="la la-window-close-o" @click="beforeDeleteZone(index, details.zoneName, details.zone)"></i>
                        </th>
                        <th>
                            <label for="zones">Добавить зону <i class="la la-plus-circle"></i></label>
                            <select name="zones"
                                    id="zones"
                                    v-model="selectedZone"
                                    @change="addColumn($event)"
                                    class="form-control col-md-12"
                            >
                                <option value="none" selected disabled hidden>Выберите зону</option>
                                <option v-for="(zone, index) in zones"
                                        :value="zone.name + '_' + index"
                                        :disabled="zone.enabled"
                                >
                                    Зона {{ zone.name }}
                                </option>
                            </select>

                        </th>
                    </tr>
                </thead>
                <tbody ref="categories">
                    <tr v-for="(category, catIndex) in dataForSave">
                        <td>{{ category.name }}</td>
                        <td><input type="checkbox" :checked="category.isUse" v-model="category.isUse"></td>
                        <td v-for="(details, index) in category.prices">
                            <span class="extra-label">Стоимость первой единицы</span>
                            <input type="number" min="0" max="9999" class="form-control col-md-12"
                                   v-model="details.price"
                                   @keydown="checkInputNumbers($event, details.price)"
                                   @keyup="checkFewZeros($event, details)"
                            >
                            <span class="extra-label">Стоимость второй единицы</span>
                            <input type="text" min="0" max="9999" class="form-control col-md-12"
                                   v-model="details.secondPrice"
                                   @keydown="checkInputNumbers($event, details.price)"
                                   @keyup="checkFewZeros($event, details)"
                            >
                        </td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>

<!--        Отображение для пользователей без прав на редактирование-->
        <div v-else>
            <table id="categories"
                   class="table-content bg-white table table-striped table-hover nowrap rounded shadow-xs border-xs mt-2 dataTable dtr-inline collapsed"
            >
                <thead>
                <tr>
                    <th>Категория</th>
                    <th>Вкл/Выкл</th>
                    <th v-for="(details, index) in headers" class="th-title">
                        {{ details.title }}
                    </th>
                </tr>
                </thead>
                <tbody ref="categories">
                <tr v-for="(category, catIndex) in dataForSave">
                    <td>{{ category.name }}</td>
                    <td><input type="checkbox" :checked="category.isUse" v-model="category.isUse" disabled></td>
                    <td v-for="(details, index) in category.prices">
                        <span class="extra-label"><b>ID цены в сервисе {{ details.service_price_id }}</b></span>
                        <br>
                        <span class="extra-label">Стоимость первой единицы</span>
                        <span class="form-control col-md-12">{{ details.price }}</span>
                        <span class="extra-label">Стоимость второй единицы</span>
                        <span class="form-control col-md-12">{{ details.secondPrice }}</span>
                    </td>
                    <td></td>
                </tr>
                </tbody>
            </table>
        </div>


        <modal v-if="showModal" @close="showModal = false">
            <span slot="body">
                {{ modalText }}
                <br>
                <button :class="btnClass" @click="showModal = !showModal">{{ btnText }}</button>
            </span>
            <span slot="footer"></span>
        </modal>

        <modal v-if="deleteModal">
            <span slot="body">
                Удалить зону с ценами ?
                <br>
                <button class="btn btn-danger" @click="deleteZone">Удалить</button>
                <button class="btn btn-secondary" @click="cancelZoneDelete()">Отмена</button>
            </span>
            <span slot="footer"></span>
        </modal>
    </div>
</template>

<script>
import Modal from "./Modal";

export default {
    name: "TariffRegionSettings",
    props: ['tariffId', 'regionId', 'categories', 'zones', 'authorId', 'userId', 'isAdmin', 'isEditable'],
    components: {
        Modal
    },
    data() {
        return {
            backUrl: '/admin/tariffs/' + this.tariffId + '/edit',
            headers: [],
            selectedZone: 'none',
            allZones: null,
            addedZones: [],
            dataForSave: [],
            showModal: false,
            modalText: '',
            btnText: 'OK',
            btnClass: 'btn btn-secondary',
            deleteModal: false,
            zoneForDelete: null
        }
    },
    mounted() {
        this.zones.forEach((zone, index) => {
            if (zone.enabled) {
                this.headers.push({
                    title: 'Зона ' + this.zones[index].name,
                    zone: this.zones[index].id,
                    zoneName: this.zones[index].name
                })
            }
        })

        this.dataForSave = this.categories
    },
    methods: {
        addColumn(event) {
            let zoneIndex = event.target.value.split('_')

            this.dataForSave.forEach((category, index) => {
                if (category.prices.length === 0) {
                    category.prices = {}
                    category.prices[zoneIndex[0]] = {}
                    category.prices[zoneIndex[0]].zone = this.zones[zoneIndex[1]].id
                    category.prices[zoneIndex[0]].price = 0
                    category.prices[zoneIndex[0]].secondPrice = 0
                } else {
                    category.prices[zoneIndex[0]] = {
                        zone: this.zones[zoneIndex[1]].id,
                        price: 0,
                        secondPrice: 0
                    }
                }
            })
            // this.headers[this.zones[zoneIndex[1]].name] = {}
            // this.headers[this.zones[zoneIndex[1]].name].name = this.zones[zoneIndex[1]].id
            // this.headers[this.zones[zoneIndex[1]].name].zone = zoneIndex[1]
            this.headers.push({
                title: 'Зона ' + this.zones[zoneIndex[1]].name,
                zone: this.zones[zoneIndex[1]].id,
                zoneName:  zoneIndex[0]
            })
            this.zones[zoneIndex[1]].enabled = true
        },
        checkInputNumbers(event, price) {
            const keysAllowed = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'Backspace'];
            const keyPressed = event.key;

            if (!keysAllowed.includes(keyPressed)) {
                event.preventDefault()
            }
        },
        checkFewZeros(event, price) {
            if (price.price[0] === '0') {
                price.price = 0
                return event.preventDefault()
            }

            // делаю по-тупому
            if (price.price.length > 4) {
                let lastValidPriceString = price.price[0] + price.price[1] + price.price[2] + price.price[3]
                price.price = lastValidPriceString * 1
                return event.preventDefault()
            }
        },
        beforeDeleteZone(index, zone, zoneId) {
            this.deleteModal = !this.deleteModal
            this.zoneForDelete = {
                index: index,
                zone: zone,
                zoneId: zoneId
            }
        },
        cancelZoneDelete() {
            this.zoneForDelete = null
            this.deleteModal = !this.deleteModal
        },
        deleteZone() {
            let categories = []
            this.deleteModal = !this.deleteModal
            this.dataForSave.forEach((category) => {
                categories.push({
                    id: category.id,
                    service_price_id: category.prices[this.zoneForDelete.zone].service_price_id
                })
                delete category.prices[this.zoneForDelete.zone]
            })

            this.headers.splice(this.zoneForDelete.index, 1)
            this.selectedZone = 'none'
            this.zones.forEach((zone) => {
                if (zone.id === this.zoneForDelete.zoneId) {
                    zone.enabled = false
                }
            })

            axios.post('/admin/tariffs/' + this.tariffId +'/region/' + this.regionId +'/delete-zone',
                {zoneId: this.zoneForDelete.zoneId, zone: this.zoneForDelete.zone, categories: categories}
            )
        },
        save() {
            axios.post('/admin/tariffs/' + this.tariffId +'/region/' + this.regionId +'/save',
                {categories: this.categories}
            ).then((response) => {
                if (response.status === 200) {
                    this.showModal = true
                    this.modalText = response.data.message
                }

                if (response.status === 500) {
                    this.showModal = true
                    this.modalText = response.data.message
                }
            }).catch((error) => {
                if (error.response.status === 500) {
                    this.showModal = true
                    this.modalText = error.response.data.message
                }
            })
        }
    }
}
</script>

<style scoped>
.th-title {
    text-align: center;
}
.extra-label {
    font-size: 12px;
}
table th,td {
    border: 1px solid #ddd
}
</style>