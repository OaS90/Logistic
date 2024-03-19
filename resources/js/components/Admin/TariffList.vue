<template>
    <div>
        <h2><span class="text-capitalize">Тарифы</span></h2>
        <div class="row">
            <div class="col-sm-2">
                <div class="d-print-none with-border">
                    <a href="/admin/tariffs/show"
                       class="btn btn-primary"
                    >
                        <span class="ladda-label"><i class="la la-plus"></i> Добавить Тариф</span>
                    </a>
                </div>
            </div>

            <tariffs-get-from-service-button v-if="isAdmin"></tariffs-get-from-service-button>

            <div class="col-sm-7">
                <div id="datatable_search_stack" class="mt-sm-0 mt-2 d-print-none">
                    <div id="crudTable_filter" class="dataTables_filter">
                        <label>
                            <input type="search" class="form-control"
                                   placeholder="Поиск..."
                                   aria-controls="crudTable"
                                   v-model="filter"
                            >
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <p>
            <button type="button" class="btn btn-secondary" @click="prevPage">Предыдущая</button>
            <button type="button" class="btn btn-secondary" @click="nextPage">Следующая</button>
            <span style="padding-left: 5px">{{ currentPage }} из {{ totalPage }}</span>
        </p>
        <table id="warehouses"
               class="table-content bg-white table table-striped table-hover nowrap rounded shadow-xs border-xs mt-2 dataTable dtr-inline collapsed has-hidden-columns">
            <thead>
                <tr>
                    <th>ID в сервисе</th>
                    <th>Наименование</th>
                    <th>Алиас</th>
                    <th>Действие</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(tariff, index) in filteredRows">
                    <td>{{ tariff.delivery_service_tariff_id }}</td>
                    <td>{{ tariff.name }}</td>
                    <td>{{ tariff.alias }}</td>
                    <td>
                        <a v-if="tariff.isAuthor || isAdmin || tariff.isEditable" :href="'tariffs/' + tariff.id + '/edit'"
                           class="btn btn-sm btn-link">
                            <i class="la la-eye"></i> Редактировать
                        </a>
                        <a v-else :href="'tariffs/' + tariff.id + '/edit'" class="btn btn-sm btn-link">
                            <i class="la la-eye"></i> Просмотр
                        </a>
                        <a v-if="tariff.alias === 'main' || tariff.isAuthor"
                            class="btn btn-sm btn-link"
                           @click="cloneTariff(tariff.id)"
                        >
                            <i class="la la-clone"></i> Клонировать
                        </a>
                        <a v-if="tariff.isAuthor || isAdmin || tariff.isEditable"
                           class="btn btn-sm btn-link"
                           @click="beforeDelete(tariff.id, index)"
                        >
                            <i class="la la-trash"></i> Удалить
                        </a>
                        <tariff-permission-request-button v-if="!tariff.isAuthor && !isAdmin && !tariff.isEditable"></tariff-permission-request-button>
                    </td>
                </tr>
            </tbody>
        </table>
        <p>
            <button type="button" class="btn btn-secondary" @click="prevPage">Предыдущая</button>
            <button type="button" class="btn btn-secondary" @click="nextPage">Следующая</button>
            <span style="padding-left: 5px">{{ currentPage }} из {{ totalPage }}</span>
        </p>

        <modal v-if="showModal">
            <div slot="body">
                <p>{{ modalText }}</p>

                <button class="btn btn-danger" @click="deleteTariff" :disabled="disableBtn">Удалить</button>
                <button class="btn btn-secondary" @click="cancelDelete">Отмена</button>
            </div>
            <span slot="footer"></span>
        </modal>
    </div>
</template>

<script>

import TariffPermissionRequestButton from "./TariffPermissionRequestButton";
import TariffsGetFromServiceButton from "./TariffsGetFromServiceButton";
import Modal from "./Modal";

export default {
    name: "TariffList",
    data() {
        return {
            pageSize: 20,
            currentPage: 1,
            filter: '',
            showModal: false,
            modalText: 'Удалить тариф?',
            tariffForDelete: null,
            disableBtn: false
        }
    },
    components: {
        TariffsGetFromServiceButton,
        TariffPermissionRequestButton,
        Modal
    },
    props: ['isAdmin', 'userId', 'tariffs'],
    mounted() {
        // this.list()
    },
    methods: {
        cancelDelete() {
            this.showModal = !this.showModal
            this.tariffForDelete = null
        },
        beforeDelete(tariffId, index) {
            this.tariffForDelete = {id: tariffId, index: index}
            this.showModal = !this.showModal
        },
        list() {
            axios.get('/admin/tariffs/get')
                .then(response => {
                    this.tariffs = response.data;
                });
        },
        nextPage() {
            if ((this.currentPage * this.pageSize) < this.tariffs.length) this.currentPage++;
        },
        prevPage() {
            if (this.currentPage > 1) this.currentPage--;
        },
        deleteTariff() {
            this.disableBtn = !this.disableBtn
            this.filteredRows.splice(this.tariffForDelete.index, 1)

            axios.delete('tariffs/' + this.tariffForDelete.id + '/delete')
                .then((response) => {
                    if (response.status === 200) {
                        this.showModal = !this.showModal
                        this.disableBtn = !this.disableBtn
                    }
                }).catch((error) => {
                    console.log(error.response.data.message)
            })
        },
        cloneTariff(tariffId) {
            axios.post('tariffs/' + tariffId + '/clone')
                .then((response) => {
                    if (response.status === 200) {
                        window.location.href = response.data.uri
                    }
                }).catch((error) => {
                if (error.response.status === 500) {
                    this.showModal = !this.showModal
                    this.modalText = error.response.data.message
                }
            })
        }
    },
    computed: {
        filteredRows() {
            return this.tariffs.filter((tariff, index) => {
                const alias = tariff.alias.toLowerCase();
                const name = tariff.name.toLowerCase()
                const searchTerm = this.filter.toLowerCase();
                let start = (this.currentPage - 1) * this.pageSize;
                let end = this.currentPage * this.pageSize;

                if (this.filter !== '') {
                    return alias.includes(searchTerm) || name.includes(searchTerm)
                }

                if (index >= start && index < end) {
                    return true
                }
            });
        },
        totalPage() {
            return Math.ceil(this.tariffs.length / this.pageSize)
        },
    },

}
</script>

<style scoped>
    a:hover {
        cursor: pointer;
    }
    a.btn-link {
        color: #7c69ef !important;
    }
</style>