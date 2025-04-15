<template>
    <div>
        <button type="button" class="btn btn-outline-success" @click="save" :disabled='this.guest'>Сохранить</button>
        <button type="button" class="btn btn btn-info" @click="excel" :disabled='this.guest'>Выгрузить Excel</button>
        <hr>
        <input type="text" class="form-control col-4 filter" v-model="filter" placeholder="Поиск...">
        <div class="wmd-view-topscroll" :style="{'width': scrollWidth + 'px'}" @scroll="topScroll" ref="scroll">
            <div class="scroll-div1" :style="{'width': tableWidth + 'px'}">
            </div>
        </div>
        <hr>
        <p>
            <button type="button" class="btn btn-secondary" @click="prevPage">Предыдущая</button>
            <button type="button" class="btn btn-secondary" @click="nextPage">Следующая</button>
            <span style="padding-left: 5px">{{ currentPage }} из {{ totalPage }}</span>
        </p>
        <div id="table-wrapper" ref="wrapper" @scroll="mainScroll">
            <table id="warehouses" class="table-content bg-white table table-striped table-hover nowrap rounded shadow-xs border-xs mt-2 dataTable dtr-inline collapsed has-hidden-columns">
                <tbody ref="table">
                    <tr style="text-align:center">
                        <th class="choose-th" style="width: 50px">Выбрать</th>
                        <th style="width: 100px">Регион</th>
                        <th style="width: 90px">Код филиала</th>
                        <th style="width: 100px">Филиал</th>
                        <th style="width: 100px">Код склада</th>
                        <th style="width: 90px">Задержка дней</th>
                        <th style="width: 90px">Квота</th>
                        <th>Настройка ТК</th>
                    </tr>
                    <tr v-for="(warehouse, id) in filteredRows" :key="`warehouse-${id}`" style="text-align:center">
                        <td><input type="checkbox" @change="toSave(warehouse)" v-model="warehouse.to_save"></td>
                        <th>{{ warehouse.region}}</th>
                        <td>{{ warehouse.code }}</td>
                        <td>{{ warehouse.name }}</td>
                        <td>{{ warehouse.warehouse_code }}</td>
                        <td>
                            <input type="number" min="0" max="9999" class="form-control wrhs-input"
                                   v-model="warehouse.delay_days"
                                   @keydown="checkInputNumbers($event, warehouse)"
                                   @keyup="checkFewZeros($event, warehouse, 'delay_days')"
                                   @focusout="checkIsEmpty($event, warehouse, 'delay_days')"
                            >
                        </td>
                        <td>
                            <input type="number" min="0" max="9999" class="form-control wrhs-input"
                                   v-model="warehouse.quote"
                                   @keydown="checkInputNumbers($event, warehouse.quote)"
                                   @keyup="checkFewZeros($event, warehouse, 'quote')"
                                   @focusout="checkIsEmpty($event, warehouse, 'quote')"
                            >
                        </td>
                        <td>
                            <button class="btn btn-secondary" style="margin-bottom: 5px"
                                    @click="changeShow(id)">
                                Показать/Скрыть настройки
                            </button>
                            <tc-settings :settings="warehouse.settings" v-show="warehouse.show_setting"></tc-settings>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p>
            <button type="button" class="btn btn-secondary" @click="prevPage">Предыдущая</button>
            <button type="button" class="btn btn-secondary" @click="nextPage">Следующая</button>
            <span style="padding-left: 5px">{{ currentPage }} из {{ totalPage }}</span>
        </p>
        <modal v-if="showModal" @close="showModal = false">
            <template #body>
                <template v-if="loading">
                    {{ modalText }}
                    <PulseLoader :loading="loading" :color="'#7c69ef'"/>
                </template>
                <template v-else>
                    {{ modalText }}
                    <br>
                    <button class="btn btn-secondary" @click="showModal = false">ОК</button>
                </template>
            </template>
        </modal>
    </div>
</template>

<script>
import Modal from './Modal.vue'
import TcSettings from './TcSettings.vue'
import {PulseLoader} from "vue3-spinner";

export default {
    name: "TransportCompanySettings",
    props: ['warehouses', 'guest'],
    components: {
        PulseLoader,
        Modal,
        TcSettings
    },
    data() {
        return {
            warehousesToSave: [],
            showModal: false,
            modalText: '',
            filter: '',
            scrollWidth: 0,
            tableWidth: 0,
            scrollPosition: 0,
            warehousesData: this.warehouses,
            pageSize: 10,
            currentPage: 1,
        }
    },
    computed: {
        filteredRows() {
            return this.warehousesData.filter((warehouse, index) => {
                const name = warehouse.name.toLowerCase();
                let region = '';

                if (!warehouse.region) {
                    region = 'empty'; // для уфы, неё почему-то нет региона
                } else {
                    region = warehouse.region.toLowerCase();
                }

                const searchTerm = this.filter.toLowerCase();
                let start = (this.currentPage - 1) * this.pageSize;
                let end = this.currentPage * this.pageSize;

                if (this.filter !== '') {
                    return name.includes(searchTerm) || region.includes(searchTerm) || warehouse.code.includes(searchTerm)
                }

                if (index >= start && index < end) return true
            });
        },
        totalPage() {
            return Math.ceil(this.warehousesData.length / this.pageSize)
        },
    },
    methods: {
        toSave(warehouse) {
            if (warehouse.to_save) {
                this.warehousesToSave.push(warehouse)
            } else {
                let index = this.warehousesToSave.indexOf(warehouse);
                if (index !== -1) {
                    this.warehousesToSave.splice(index, 1);
                }
            }
        },
        topScroll(e) {
            let currentScrollPosition = e.srcElement.scrollLeft
            this.$refs.wrapper.scrollTo(currentScrollPosition, 0)
        },
        mainScroll(e) {
            let currentScrollPosition = e.srcElement.scrollLeft
            this.$refs.scroll.scrollTo(currentScrollPosition, 0)
        },
        changeShow(id) {
            this.filteredRows[id].show_setting = !this.filteredRows[id].show_setting
        },
        nextPage() {
            if((this.currentPage * this.pageSize) < this.warehousesData.length) this.currentPage++;
        },
        prevPage() {
            if(this.currentPage > 1) this.currentPage--;
        },
        save() {
            if (this.warehousesToSave.length === 0) {
                this.showModal = !this.showModal
                this.modalText = 'Не выбрано ни одного склада для обновления'
            } else {
                axios.post('save-tc-settings', this.warehousesToSave).then(response => {
                    this.showModal = !this.showModal
                    this.modalText = response.data.message
                }).catch(errors => {
                    this.showModal = !this.showModal
                    this.modalText = errors.response.data.message
                })

            }
        },
        checkInputNumbers(event) {
            const keysAllowed = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'Backspace'];
            const keyPressed = event.key;

            if (!keysAllowed.includes(keyPressed)) {
                event.preventDefault()
            }
        },
        checkIsEmpty(event, warehouse, type) {
            if (warehouse[type] === 0) {
                warehouse[type] = 0
                return event.preventDefault()
            }
        },
        checkFewZeros(event, warehouse, type) {
            if (warehouse[type][0] === '0') {
                warehouse[type] = 0
                return event.preventDefault()
            }

            // делаю по-тупому
            if (warehouse[type].length > 4) {
                let lastValidPriceString = warehouse[type][0] + warehouse[type][1] + warehouse[type][2] + warehouse[type][3]
                warehouse[type] = lastValidPriceString * 1
                return event.preventDefault()
            }
        },
        excel() {
            axios.get('export', {responseType: 'blob'}).then((response) => {
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'tc-quotes.xlsx');
                document.body.appendChild(link);
                link.click();
            })
        },
    }
}
</script>

<style scoped>
input[type=checkbox] {
    transform: scale(1.5);
}
.wrhs-input {
    width: 70px;
}
</style>
