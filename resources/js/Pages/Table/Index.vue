<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Quan ly ban
      </h2>
    </template>
    <div
      class="py-12"
      :class="`${checkRole('waiter') ? 'sm:grid grid-cols-3' : ''}`"
    >
      <div
        class="
          max-w-7xl
          mx-auto
          sm:px-6
          lg:px-8
          border-b-2 border-black
          sm:border-none
        "
        :class="`${checkRole('waiter') ? 'col-span-2' : ''}`"
      >
        <jet-action-message :on="form.recentlySuccessful" class="mr-3">
          <span class="text-green-500">{{ $page.props.flash.message }}</span>
        </jet-action-message>
        <!-- HOST -->
        <div
          class="flex mt-5 flex-wrap px-4 sm:px-0 sm:justify-start"
          v-if="checkRole('host') && filterTableByStatus(0).length > 0"
        >
          <div
            class="
              rounded
              sm:w-1/5
              mr-3
              mb-3
              py-3
              px-5
              capitalize
              cursor-pointer
              bg-green-500
            "
            v-for="(table, k) in filterTableByStatus(0)"
            @click="handleClick($event, table)"
            :key="k"
          >
            Table {{ k + 1 }} - {{ table.table_id }}
          </div>
        </div>
        <!-- BUSBOY -->
        <div
          v-else-if="checkRole('busboy') && filterTableByStatus(-1).length > 0"
          class="flex mt-5 flex-wrap px-4 sm:px-0"
        >
          <div
            class="
              rounded
              w-1/5
              mr-3
              mb-3
              py-3
              px-5
              capitalize
              cursor-pointer
              bg-gray-500
              text-white
            "
            v-for="(table, k) in filterTableByStatus(-1)"
            @click="cleanAndPrepare(table)"
            :key="k"
          >
            Table {{ k + 1 }} - {{ table.table_id }}
          </div>
        </div>
        <!-- WAITER -->
        <div
          class="flex mt-5 flex-wrap px-4 sm:px-0"
          v-else-if="
            checkRole('waiter') &&
            tables.filter(
              (t) =>
                t.table_status !== 0 &&
                t.table_status !== -1 &&
                t.employee_id === $page.props.employee.id
            ).length > 0
          "
        >
          <inertia-link
            :href="
              route('tables.order.place', {
                table_id: table.table_id,
              })
            "
            class="rounded mr-3 mb-3 py-3 px-5 capitalize cursor-pointer"
            :class="
              table.table_status === 1
                ? `bg-yellow-400 text-white`
                : table.table_status === -1
                ? `bg-gray-500 text-white`
                : table.table_status === 2
                ? `bg-white text-dark`
                : `bg-green-500`
            "
            v-for="(table, k) in tables.filter(
              (t) => t.table_status !== 0 && t.table_status !== -1
            )"
            :key="k"
          >
            Ban so {{ table.table_id }}
          </inertia-link>
        </div>
        <div class="flex mt-5 flex-wrap px-4 sm:px-0" v-else>
          Chua co ban nao san co.
        </div>

        <!-- TABLE DIALOG -->
        <jet-dialog-modal :show="show" @close="closeModal">
          <template #title>
            <div class="text-lg">
              Chi dinh nhan vien cho ban so {{ form.table_id }}.
            </div>
          </template>
          <template #content>
            <table class="w-full">
              <thead>
                <tr class="bg-gray-100">
                  <th class="px-4 py-2">Ten nhan vien</th>
                  <th class="px-4 py-2"># Cac ban dang phuc vu</th>
                  <th class="px-4 py-2">Hanh dong</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="row in employeesComp" :key="row.id">
                  <td class="border px-4 py-2">{{ row.name }}</td>
                  <td class="border px-4 py-2">
                    {{ row.tableAssigned.join(",") }}
                  </td>
                  <td class="border px-4 py-2">
                    <jet-primary-button
                      @click="assign(row)"
                      :disabled="form.processing"
                      v-show="checkRole(`host`)"
                      class="mr-2"
                    >
                      Gan ban
                    </jet-primary-button>
                    <jet-primary-button
                      @click="clean(row)"
                      :disabled="form.processing"
                      v-show="checkRole(`busboy`)"
                      class="mr-2"
                    >
                      Don ban
                    </jet-primary-button>
                    <inertia-link
                      v-show="checkRole('waiter')"
                      :href="
                        route('tables.order.place', {
                          table_id: form.table_id,
                        })
                      "
                      class="
                        inline-flex
                        items-center
                        px-4
                        py-2
                        bg-white
                        border border-gray-300
                        rounded-md
                        font-semibold
                        text-xs text-gray-700
                        uppercase
                        tracking-widest
                        shadow-sm
                        hover:text-gray-500
                        focus:outline-none
                        focus:border-blue-300
                        focus:shadow-outline-blue
                        active:text-gray-800
                        active:bg-gray-50
                        transition
                        ease-in-out
                        duration-150
                      "
                    >
                      Dat mon
                    </inertia-link>
                  </td>
                </tr>
              </tbody>
            </table>
          </template>
          <template #footer>
            <jet-secondary-button @click="closeModal">
              Huy
            </jet-secondary-button>
          </template>
        </jet-dialog-modal>
        <div class="px-5 sm:px-0">
          <div class="block mt-5">Chu thich:</div>
          <div
            class="inline-block mr-3 mb-2 py-1 px-5 capitalize font-bold"
            :class="
              status.id === 1
                ? `bg-yellow-400 text-white`
                : status.id === -1
                ? `bg-gray-500 text-white`
                : status.id === 2
                ? `bg-white text-dark`
                : `bg-green-500`
            "
            v-for="(status, k) in tableStatus"
            :key="k"
          >
            {{ status.name }}
          </div>
        </div>
      </div>

      <!-- ORDER SIDE -->
      <div class="py-2" v-show="checkRole('waiter')">
        <jet-action-message :on="orderForm.recentlySuccessful" class="mr-3">
          <span class="text-green-500">{{ $page.props.flash.message }}</span>
        </jet-action-message>
        <h2
          class="font-semibold text-xl text-gray-800 leading-tight pl-5 sm:pl-0"
        >
          Danh sach don dat hang.
        </h2>
        <div class="px-5 sm:px-0">
          <div class="block my-2">Chu thich:</div>
          <!-- STATUS LEGEND -->
          <div
            class="inline-block mb-2 mr-2 py-1 px-5 capitalize font-bold"
            :class="
              status.id === 2
                ? `bg-yellow-400 text-white`
                : status.id === 1
                ? `bg-green-300`
                : status.id === -1
                ? `bg-gray-500 text-white`
                : `bg-white`
            "
            v-for="(status, k) in orderStatus"
            :key="k"
          >
            {{ status.name }}
          </div>
        </div>

        <!-- ORDER LIST -->
        <ul class="list-inside bg-rose-200" v-if="ordersComp.length > 0">
          <template v-for="(order, k) in ordersComp" :key="k">
            <li
              class="inline-block px-4 py-2 cursor-pointer mr-2"
              @click="orderClick(order)"
              :class="
                order.status === 2
                  ? `bg-yellow-400 text-white`
                  : order.status === 1
                  ? `bg-green-300`
                  : order.status === -1
                  ? `bg-gray-500 text-white`
                  : `bg-white`
              "
            >
              - Don hang {{ k + 1 }} - Ban {{ order.table_id }}
            </li>
          </template>
        </ul>
        <div class="flex mt-5 flex-wrap px-4 sm:px-0" v-else>
          Khong co don hang nao.
        </div>
        <!-- ORDER DIALOG -->
        <jet-dialog-modal :show="orderDialog" @close="orderDialog = false">
          <template #title>
            <div class="text-lg">Lua chon</div>
          </template>
          <template #content>
            <div class="border px-4 py-2">
              <jet-primary-button
                @click="delivered()"
                :disabled="orderForm.processing"
                class="mr-2"
                v-show="orderForm.status !== -1"
              >
                Da duoc van chuyen
              </jet-primary-button>
              <a
                @click="paybill"
                :disabled="orderForm.processing"
                target="_blank"
                class="
                  mr-2
                  inline-flex
                  items-center
                  justify-center
                  px-4
                  py-2
                  bg-green-500
                  hover:bg-green-700
                  border border-transparent
                  rounded-md
                  font-semibold
                  text-xs text-white
                  uppercase
                  tracking-widest
                  hover:bg-red-500
                  focus:outline-none
                  focus:border-red-700
                  focus:shadow-outline-red
                  active:bg-red-600
                  transition
                  ease-in-out
                  duration-150
                  mr-2
                "
              >
                Thanh toan
              </a>
            </div>
          </template>
          <template #footer>
            <jet-secondary-button @click="orderDialog = false">
              Huy
            </jet-secondary-button>
          </template>
        </jet-dialog-modal>
      </div>
    </div>
  </app-layout>
</template>

<script>
import JetDialogModal from "../../Jetstream/DialogModal.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import JetLabel from "../../Jetstream/Label.vue";
import JetInput from "../../Jetstream/Input.vue";
import JetInputError from "../../Jetstream/InputError";
import JetSelect from "../../Jetstream/Select";
import JetSecondaryButton from "../../Jetstream/SecondaryButton";
import JetSuccessButton from "../../Jetstream/SuccessButton";
import JetPrimaryButton from "../../Jetstream/PrimaryButton";
import JetActionMessage from "@/Jetstream/ActionMessage";
const INIT_TABLE = {
  table_id: "",
  table_status: "",
  employee_id: "",
};
const INIT_ORDER = {
  id: "",
  status: null,
  table_id: null,
  waiter_id: null,
  datas: [],
};
export default {
  components: {
    AppLayout,
    JetSelect,
    JetLabel,
    JetInputError,
    JetInput,
    JetDialogModal,
    JetSuccessButton,
    JetSecondaryButton,
    JetActionMessage,
    JetPrimaryButton,
  },
  props: {
    tables: Array,
    orders: Array,
  },
  data() {
    return {
      show: false,
      orderDialog: false,
      form: this.$inertia.form(INIT_TABLE, {
        resetOnSuccess: false,
      }),
      orderForm: this.$inertia.form(INIT_ORDER, {
        resetOnSuccess: false,
      }),
      employees: [],
      typeSelected: false,
      delay: 700,
      clicks: 0,
      timer: null,
      tableStatus: [
        {
          id: 0,
          name: "San sang",
        },
        {
          id: 1,
          name: "Da duoc dat",
        },
        {
          id: -1,
          name: "Chua san sang",
        },
        {
          id: 2,
          name: "Dang chiem giu",
        },
      ],
      orderStatus: [
        {
          id: -1,
          name: "Da duoc van chuyen",
        },
        {
          id: 0,
          name: "San sang van chuyen",
        },
        {
          id: 1,
          name: "Dang chuan bi",
        },
        {
          id: 2,
          name: "Dang cho",
        },
      ],
    };
  },
  created() {
    Echo.channel("table_assigned")
      .listen("TableAssignedEvent", (e) => {
        let index = this.tables.findIndex(
          (table) => table.table_id === e.table.table_id
        );
        if (index > -1) {
          this.tables[index].table_status = e.table.table_status;
        }
      })
      .listen("TableAssignedToWaiter", (e) => {
        console.log("TableAssignedToWaiter");
        console.log(e);
      });
    Echo.private(`App.Models.User.${this.$page.props.user.id}`).notification(
      (notification) => {
        let order = notification[0].order;
        let table = notification[0].table;
        if (order) {
          let index = this.orders.findIndex((item) => item.id === order.id);
          if (index > -1) {
            this.orders[index].status = order.status;
          }
        }
        if (table) {
          let index = this.tables.findIndex(
            (item) => item.table_id === table.table_id
          );
          if (index > -1) {
            this.tables[index].table_status = table.table_status;
          }
        }
      }
    );
    Echo.channel("order.ready").listen("ChefOrderDoneEvent", (e) => {
      let index = this.orders.findIndex((order) => order.id === e.order.id);
      if (index > -1) {
        this.orders[index].status = e.order.status;
      }
    });
  },

  computed: {
    employeesComp() {
      return this.employees;
    },
    ordersComp() {
      return this.orders.filter(
        (order) =>
          order.status !== -2 &&
          order.waiter_id === this.$page.props.employee.id
      );
    },
    tablesComp() {
      return this.tables;
    },
  },
  methods: {
    filterTableByStatus(status) {
      return this.tablesComp.filter((t) => t.table_status === status);
    },
    checkRole(roleName) {
      return this.$root.roleArrayComputed.some((role) => role === roleName);
    },
    delivered() {
      let orderForm = this.orderForm;
      orderForm.status = -1;
      orderForm.put(this.route("chef.orders.update", { id: orderForm.id }), {
        onSuccess: () => {
          this.orderDialog = false;
          this.orderForm.reset();
        },
      });
    },
    async paybill() {
      await this.$inertia.visit(
        this.route("orders.show.paybill", this.orderForm.table_id)
      );
      this.orderForm = {
        ...this.orderForm,
        status: -2,
      };
      this.orderForm.put(
        this.route("chef.orders.update", { id: this.orderForm.table_id }),
        {
          onSuccess: () => {
            this.orderDialog = false;
            this.orderForm.reset();
          },
        }
      );
    },
    orderClick(order) {
      if (order.status === 0 || order.status === -1) {
        this.orderDialog = true;
        this.orderForm = {
          ...this.orderForm,
          id: order.id,
          table_id: order.table_id,
          status: order.status,
          datas: JSON.parse(order.data),
        };
      }
    },
    cleanAndPrepare(row) {
      this.form.put(this.route("tables.update", { id: row.table_id }), {
        onSuccess: () => {
          this.fetchWaiters();
          this.closeModal();
        },
      });
    },
    clean(row) {
      this.form.employee_id = row.employee_id;
      this.form.id = this.form.table_id;
      this.form.put(this.route("tables.update", { table: this.form.id }), {
        onSuccess: () => {
          this.fetchWaiters();
          this.closeModal();
        },
      });
    },
    closeModal() {
      this.show = false;
      this.form.reset();
    },
    handleClick(event, table) {
      this.clicks++;
      if (this.clicks === 1) {
        var self = this;
        this.timer = setTimeout(function () {
          self.clicks = 0;
          self.form = { ...self.form, ...table };
          self.show = true;
        }, this.delay);
      } else {
        clearTimeout(this.timer);
        this.clicks = 0;
      }
    },
    assign(row) {
      this.form.employee_id = row.id;
      this.form.id = this.form.table_id;
      this.form.put(this.route("tables.update", { table: this.form }), {
        onSuccess: () => {
          this.fetchWaiters();
          this.closeModal();
        },
      });
    },
    createTable() {
      this.form.post(this.route("tables.store"));
    },
    fetchWaiters() {
      fetch(this.route("frontend.employees.index"))
        .then((res) => res.json())
        .then((data) => (this.employees = data));
    },
  },
  mounted() {
    this.fetchWaiters();
  },
};
</script>

<style lang="scss" scoped>
</style>