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
                        </th>
                    </tr>
                </thead>
                <tbody ref="categories">
                    <tr v-for="(category, catIndex) in dataForSave">
                        <td>{{ category.name }}</td>
                        <td><input type="checkbox" :checked="category.isUse" v-model="category.isUse"></td>
                        <td v-for="(details, index) in category.prices">
                            <span class="extra-label">Стоимость первой единицы</span>
                            <input class="form-control col-md-12"
                                   v-model="details.price"
                                   :class="{'error': details.price === null }"
                                   @input="checkPriceInput($event, details, 'price')"
                            >
                            <span class="extra-label">Стоимость второй единицы</span>
                            <input :class="{'error': details.secondPrice === null }"  class="form-control col-md-12"
                                   v-model="details.secondPrice"
                                   @input="checkPriceInput($event, details, 'secondPrice')"
                            >
                        </td>
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
<!--                        <span class="extra-label"><b>ID цены в сервисе {{ details.service_price_id }}</b></span>-->
<!--                        <br>-->
                        <span class="extra-label">Стоимость первой единицы</span>
                        <span class="form-control col-md-12">{{ details.price }}</span>
                        <span class="extra-label">Стоимость второй единицы</span>
                        <span class="form-control col-md-12">{{ details.secondPrice }}</span>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>


        <modal v-if="showModal" @close="showModal = false">
            <template #body>
                {{ modalText }}
                <br>
                <button :class="btnClass" @click="showModal = !showModal">{{ btnText }}</button>
            </template>
        </modal>

        <modal v-if="deleteModal">
            <template #body>
                Удалить зону с ценами ?
                <br>
                <button class="btn btn-danger" @click="deleteZone">Удалить</button>
                <button class="btn btn-secondary" @click="cancelZoneDelete()">Отмена</button>
            </template>
        </modal>
    </div>
</template>

<script>
import Modal from "./Modal.vue";

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
        checkPriceInput(event, detailsObj, key) {
            let value = event.target.value.replace(/\D/g, '');
            if (value.length > 4) value = value.slice(0, 4);
            event.target.value = value;
            detailsObj[key] = value;
        },
        checkInputNumbers(event) {
            const keysAllowed = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'Backspace'];
            const keyPressed = event.key;

            if (!keysAllowed.includes(keyPressed)) {
                event.preventDefault()
            }
        },
        checkIsEmpty(event, price, type) {
            if (price[type].length === 0) {
                price[type] = 0
                return event.preventDefault()
            }
        },
        checkFewZeros(event, price, type) {
            if (price[type][0] === '0') {
                price[type] = 0
                return event.preventDefault()
            }

            // делаю по-тупому
            if (price[type].length > 4) {
                let lastValidPriceString = price[type][0] + price[type][1] + price[type][2] + price[type][3]
                price[type] = lastValidPriceString * 1
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
                categories.push(category.id)
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
.error {
    border: 1px solid red;
}
</style>
