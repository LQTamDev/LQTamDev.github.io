<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Place Order
      </h2>
    </template>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <jet-action-message :on="form.recentlySuccessful" class="mr-3">
          <span class="text-green-500">{{ $page.props.flash.message }}</span>
        </jet-action-message>

        <jet-action-message :on="$page.props.errors" class="mr-3">
          <span
            class="text-red-500"
            v-for="(error, key) in $page.props.errors"
            :key="key"
          >
            {{ key }} : {{ error }}</span
          >
        </jet-action-message>
        <div class="text-lg">Add Item To Order:</div>
        <table>
          <thead>
            <tr>
              <th>Type</th>
              <th>Name</th>
              <th>Quantity</th>
              <th>Stock</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>
                <select
                  id="type"
                  ref="type"
                  @change="typeChanged()"
                  v-model="form.type"
                >
                  <option value="">Select One</option>
                  <option v-for="(type, key) in types" :key="key" :value="type">
                    {{ type }}
                  </option>
                </select>
              </td>
              <td>
                <select
                  id="name"
                  @change="itemChanged($event)"
                  ref="name"
                  v-model="form.name"
                >
                  <option value="">Select Type First</option>
                  <option
                    v-for="(menu, key) in filterByType"
                    :key="key"
                    :value="menu.food_id"
                  >
                    {{ menu.name }}
                  </option>
                </select>
              </td>
              <td>
                <input
                  :disabled="form.name === ''"
                  type="number"
                  ref="quantity"
                  min="0"
                  @change="quantityChanged($event)"
                  v-model.number="form.quantity"
                />
                <!-- <span v-show="quantityError" class="text-red-500">
                  Out of stock
                </span> -->
              </td>
              <td>
                <span v-if="filterByName[0]" class="text-red-500">
                  {{ filterByName[0].stock.quantity }}
                </span>
                <span v-else class="text-red-500"> 0 </span>
              </td>
              <td>
                <button
                  type="button"
                  @click="addItem"
                  :disabled="form.quantity > quantity"
                  :class="`px-4 
                  
          py-2 inline-flex items-center justify-center border border-transparent 
          rounded-md font-semibold text-xs uppercase tracking-widest ${
            form.quantity > quantity || form.quantity === 0
              ? `cursor-not-allowed bg-gray-300`
              : `  bg-blue-500 hover:bg-blue-700  text-white   
          hover:bg-red-500 focus:outline-none focus:border-red-700 
          focus:shadow-outline-red active:bg-red-600 transition ease-in-out duration-150`
          }`"
                >
                  Them
                </button>
              </td>
            </tr>
          </tbody>
        </table>
        <div class="flex mt-5 flex-wrap px-4 sm:px-0">
          <div
            class="
              mr-3
              mt-3
              py-3
              px-5
              bg-green-500
              hover:bg-yellow-400
              hover:text-white
              capitalize
              cursor-pointer
            "
            v-for="(item, k) in order"
            :key="k"
          >
            {{ item.name }} - Qty: {{ item.stock.quantity }}
          </div>
        </div>
        <button
          @click="placeOrder"
          v-if="order.length > 0"
          class="mt-5 bg-green-500 hover:bg-green-300 mr-3 p-2"
        >
          Dat mon
        </button>
        <button v-else class="mt-5 bg-gray-300 cursor-not-allowed mr-3 p-2">
          Dat mon
        </button>
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
import JetPrimaryButton from "../../Jetstream/PrimaryButton";
import JetActionMessage from "@/Jetstream/ActionMessage";
const INIT_TABLE = {
  table_id: "",
  status: "",
  waiter_id: "",
  quantity: 0,
  name: "",
  datas: [],
  type: "",
  food_id: null,
};
export default {
  components: {
    AppLayout,
    JetSelect,
    JetLabel,
    JetInputError,
    JetInput,
    JetDialogModal,
    JetSecondaryButton,
    JetActionMessage,
    JetPrimaryButton,
  },
  props: ["menus", "table", "types"],
  data() {
    return {
      show: false,
      form: this.$inertia.form(INIT_TABLE, {}),
      delay: 700,
      clicks: 0,
      timer: null,
      quantity: 0,
      quantityError: null,
      order: [],
    };
  },
  computed: {
    filterByType() {
      return this.menus.filter((item) => item.type === this.form.type) ?? [];
    },
    recentlySuccessful() {
      return this.form.recentlySuccessful;
    },
    filterByName() {
      return this.menus.filter((item) => item.food_id === this.form.name) ?? [];
    },
  },
  methods: {
    async paybill() {
      await this.$inertia.visit(
        this.route("orders.show.paybill", this.table.table_id)
      );
      this.form = {
        ...this.form,
        status: -2,
        table_id: this.table.table_id,
      };
      this.orderForm;
      this.form.put(this.route("chef.orders.update", { id: order.id }), {
        onSuccess: () => {
          this.orderDialog = false;
          this.orderForm.reset();
        },
      });
    },
    closeModal() {
      this.form.reset();
      this.quantity = 0;
      this.quantityError = null;
    },
    addItem() {
      if (this.form.quantity <= this.quantity && this.form.quantity > 0) {
        let datas = [];
        this.form = {
          ...this.form,
          datas: datas,
          table_id: this.table.table_id,
          waiter_id: this.table.employee_id,
        };
        let index = this.getMenuItemIndex(this.form.name);
        this.menus[index].stock.quantity -= this.form.quantity;
        let item = this.menus[index];
        let itemOrderIndex = this.order.findIndex(
          (order) => order.food_id === this.form.name
        );
        if (itemOrderIndex > -1) {
          this.order[itemOrderIndex].stock.quantity += this.form.quantity;
        } else {
          this.order.push({ ...item, stock: { ...item.stock,quantity: this.form.quantity } });
        }
        this.closeModal();
      }
    },
    placeOrder() {
      this.form = {
        ...this.form,
        datas: this.order,
        table_id: this.table.table_id,
        waiter_id: this.table.employee_id,
      };
      this.form.post(
        this.route("tables.order.store", { table_id: this.table.table_id }),
        {
          onSuccess: () => {
            setTimeout(() => {
              this.$inertia.visit(this.route("tables.index"));
            }, 1000);
          },
        }
      );
    },
    quantityChanged(e) {
      if (e.target.value > this.quantity) {
        this.quantityError = true;
      } else {
        this.quantityError = null;
      }
    },
    itemChanged(e) {
      this.quantity = this.menus.filter(
        (item) => item.food_id === parseInt(e.target.value)
      )[0].stock.quantity;
    },
    handleClick(event, menu) {
      this.clicks++;
      if (this.clicks === 1) {
        var self = this;
        this.timer = setTimeout(function () {
          self.clicks = 0;
        }, this.delay);
      } else {
        clearTimeout(this.timer);
        this.clicks = 0;
      }
    },
    getMenuItemIndex(value) {
      return this.menus.findIndex((item) => item.food_id === value);
    },
    typeChanged() {
      this.form.reset("name", "quantity");
    },
    quantityChange(item) {
      let index = this.getMenuItemIndex(item.food_id);
      if (item.quantity > item.stock.quantity) {
        this.menus[index].quantityError = "Out of stock";
      } else {
        this.menus[index].quantityError = null;
        this.menus[index].quantity = item.quantity;
      }
    },
  },
};
</script>