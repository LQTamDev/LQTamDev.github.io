<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Manage Order
      </h2>
    </template>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <jet-action-message :on="form.recentlySuccessful" class="mr-3">
          <span class="text-green-300">{{ $page.props.flash.message }}</span>
        </jet-action-message>
        <div
          class="flex mt-5 flex-wrap px-4 sm:px-0"
          v-if="orderComp.length > 0"
        >
          <div
            class="rounded w-1/5 mr-3 mb-3 py-3 px-5 capitalize cursor-pointer"
            :class="
              order.status === 2 ? `bg-yellow-400 text-white` : `bg-green-300`
            "
            v-for="(order, k) in orderComp"
            @click="handleClick($event, order)"
            :key="k"
          >
            Order {{ k + 1 }} - Table {{ order.table_id }}
          </div>
        </div>
        <div class="flex mt-5 flex-wrap px-4 sm:px-0" v-else>
          Khong co don mon nao.
        </div>
        <jet-dialog-modal :show="show" @close="closeModal">
          <template #title>
            <div class="text-lg">Danh sach mon an dat hang</div>
          </template>
          <template #content>
            <div class="block mb-4 flex flex-wrap">
              <div
                class="
                  border
                  inline
                  rounded
                  mr-3
                  mb-3
                  py-1
                  px-5
                  capitalize
                  cursor-pointer
                "
                v-for="(food, k) in form.datas"
                :key="k"
              >
                {{ food.name }} - {{ food.stock.quantity }}
              </div>
            </div>
            <div class="border px-4 py-2">
              <jet-primary-button
                @click="somePending()"
                :disabled="form.processing"
                class="mr-2"
              >
                Dang chuan bi
              </jet-primary-button>
              <jet-success-button
                @click="readyDelivery()"
                :disabled="form.processing"
                class="mr-2"
              >
                San sang
              </jet-success-button>
            </div>
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
            class="
              inline-block
              mr-3
              py-1
              px-5
              capitalize
              cursor-pointer
              font-bold
            "
            :class="
              status.id === 2 ? `bg-yellow-400 text-white` : `bg-green-300`
            "
            v-for="(status, k) in orderStatus"
            :key="k"
          >
            {{ status.name }}
          </div>
        </div>
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
import JetSuccessButton from "../../Jetstream/SuccessButton";
import JetSecondaryButton from "../../Jetstream/SecondaryButton";
import JetPrimaryButton from "../../Jetstream/PrimaryButton";
import JetActionMessage from "@/Jetstream/ActionMessage";
const INIT_TABLE = {
  id: "",
  status: null,
  waiter_id: "",
  table_id: "",
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
    JetSecondaryButton,
    JetSuccessButton,
    JetActionMessage,
    JetPrimaryButton,
  },
  props: {
    orders: Array,
  },
  data() {
    return {
      show: false,
      form: this.$inertia.form(INIT_TABLE, {
        resetOnSuccess: false,
      }),
      orderStatus: [
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
    Echo.channel("order.created").listen("OrderCreatedEvent", (e) => {
      this.orders.push(e.order);
    });
  },
  computed: {
    orderComp() {
      return this.orders.filter(
        (order) =>
          order.status !== -1 && order.status !== -2 && order.status !== 0
      );
    },
  },
  methods: {
    closeModal() {
      this.show = false;
      this.form.reset();
    },
    handleClick(event, order) {
      this.show = true;
      this.form = {
        ...this.form,
        id: order.id,
        status: order.status,
        table_id: order.table_id,
        waiter_id: order.waiter_id,
        datas: JSON.parse(order.data),
      };
    },
    somePending() {
      this.form.status = 1;
      this.form.put(
        this.route("chef.orders.update", { table_id: this.form.table_id }),
        {
          onSuccess: () => {
            this.closeModal();
          },
        }
      );
    },
    readyDelivery() {
      this.form.status = 0;
      this.form.put(
        this.route("chef.orders.update", { table_id: this.form.table_id }),
        {
          onSuccess: () => {
            this.closeModal();
          },
        }
      );
    },
  },
};
</script>

<style lang="scss" scoped>
</style>