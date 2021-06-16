<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Manage Menu
      </h2>
    </template>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white px-5">
        <jet-action-message :on="form.recentlySuccessful" class="mr-3">
          <span class="text-green-500">{{ $page.props.flash.message }}</span>
        </jet-action-message>
        <jet-input-error
          v-if="updateError"
          :message="updateError"
          class="mt-2"
        />
        <jet-primary-button @click="show = true" class="my-3">
          Them hang ton kho
        </jet-primary-button>
        <div class="flex mt-5 flex-wrap">
          <div
            class="
              rounded
              items-center
              sm:w-1/6
              mr-3
              mb-3
              py-3
              pr-5
              capitalize
              cursor-pointer
            "
            v-for="(item, k) in inventories"
            :key="k"
          >
            <div class="inline-block sm:block">
              {{ item.name }}
            </div>
            <jet-input
              :id="`quantity${item.id}`"
              type="number"
              v-model="item.quantity"
              :ref="`quantity${item.id}`"
              min="0"
              max="1000"
              @change="change(item, $event)"
            />
          </div>
        </div>

        <!-- CREATE MENU ITEM DIALOG FORM -->
        <jet-dialog-modal :show="show" @close="closeModal">
          <template #title>
            <div class="text-lg">Create Inventory</div>
          </template>
          <template #content>
            <p
              class="text-red-500 text-center py-3"
              v-show="$page.props.errorBags.item_existed"
            >
              {{ $page.props.errorBags.item_existed }}
            </p>
            <div class="flex justify-around">
              <div class="mb-4">
                <jet-label for="food_id" value="Select food" />
                <select v-model="form.menu_id">
                  <option
                    v-for="menu in menus"
                    :key="menu.food_id"
                    :value="menu.food_id"
                  >
                    {{ menu.name }}
                  </option>
                </select>
                <jet-input-error
                  v-if="form.errors.menu_id"
                  :message="form.errors.menu_id"
                  class="mt-2"
                />
              </div>
              <div class="mb-4">
                <jet-label for="name" value="Name" />
                <jet-input
                  id="type"
                  type="text"
                  class="mt-1"
                  v-model="form.name"
                  ref="name"
                  autocomplete="name"
                  required
                />
                <jet-input-error
                  v-if="form.errors.name"
                  :message="form.errors.name"
                  class="mt-2"
                />
              </div>
              <div class="mb-4">
                <jet-label for="quantity" value="Quantity" />
                <jet-input
                  id="quantity"
                  type="number"
                  ref="quantity"
                  v-model="form.quantity"
                  autocomplete="quantity"
                  min="1"
                  max="1000"
                  required
                />
                <jet-input-error
                  v-if="form.errors.quantity"
                  :message="form.errors.quantity"
                  class="mt-2"
                />
              </div>
            </div>
          </template>
          <template #footer>
            <jet-primary-button
              @click="createItem"
              v-if="!editMode"
              :disabled="form.processing"
              class="mr-2"
            >
              Them
            </jet-primary-button>
            <jet-primary-button
              @click="updateItem"
              v-if="editMode"
              :disabled="form.processing"
              class="mr-2"
            >
              Sua
            </jet-primary-button>
            <jet-secondary-button @click="closeModal">
              Huy
            </jet-secondary-button>
          </template>
        </jet-dialog-modal>

        <!-- DELETE DIALOG CONFIRM -->
        <jet-dialog-modal :show="confirmDelete" @close="closeModal">
          <template #title> Delete Item </template>

          <template #content>
            Are you sure you want to delete this Item? Once this item is
            deleted, all of its resources and data will be permanently deleted.
          </template>

          <template #footer>
            <jet-secondary-button @click="confirmDelete = false">
              Huy
            </jet-secondary-button>
            <jet-danger-button
              class="ml-2"
              @click.native="deleteItem"
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
            >
              Xoa
            </jet-danger-button>
          </template>
        </jet-dialog-modal>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import JetFormSection from "../../Jetstream/FormSection.vue";
import JetSelect from "../../Jetstream/Select.vue";
import JetLabel from "../../Jetstream/Label.vue";
import JetInputError from "../../Jetstream/InputError.vue";
import JetInput from "../../Jetstream/Input.vue";
import JetPrimaryButton from "../../Jetstream/PrimaryButton.vue";
import JetButton from "../../Jetstream/Button.vue";
import JetDialogModal from "../../Jetstream/DialogModal.vue";
import JetSuccessButton from "../../Jetstream/SuccessButton.vue";
import JetDangerButton from "../../Jetstream/DangerButton.vue";
import JetSecondaryButton from "../../Jetstream/SecondaryButton.vue";
import JetActionMessage from "@/Jetstream/ActionMessage";
const INIT_ITEM = {
  quantity: 1,
  food_id: null,
  name: "",
  menu_id: null,
  food: {},
};
const FORM_TITLE = {
  create: "Create",
  edit: "Edit",
};
export default {
  components: {
    AppLayout,
    JetFormSection,
    JetSelect,
    JetLabel,
    JetInputError,
    JetInput,
    JetPrimaryButton,
    JetButton,
    JetDialogModal,
    JetSuccessButton,
    JetSecondaryButton,
    JetDangerButton,
    JetActionMessage,
  },
  props: ["inventories", "menus"],
  data() {
    return {
      updateError: "",
      count: 0,
      show: false,
      form: this.$inertia.form(INIT_ITEM, {
        resetOnSuccess: true,
      }),
      editMode: false,
      confirmDelete: false,
    };
  },
  methods: {
    closeModal() {
      this.show = false;
      this.confirmDelete = false;
      this.form.reset();
      this.editMode = false;
      this.formTitle = FORM_TITLE.create;
      if (this.form.errors) this.form.clearErrors();
    },
    createItem() {
      this.form.post(this.route("inventories.store"), {
        onSuccess: () => {
          if (!this.$page.props.errorBags.item_existed) {
            this.closeModal();
          }
        },
      });
    },
    deleteRow(row) {
      this.form = {
        ...this.form,
        food_id: row.food_id,
        name: row.name,
        quantity: row.quantity,
        menu_id: row.menu_id,
        food: row.food,
      };
      this.confirmDelete = true;
    },
    deleteItem() {
      this.form.delete(
        this.route("inventories.destroy", { food_id: this.form.food_id }),
        {
          onSuccess: () => {
            this.confirmDelete = false;
          },
        }
      );
    },
    updateItem() {
      this.form.put(
        this.route("inventories.update", { food_id: this.form.food_id }),
        {
          onSuccess: () => this.closeModal(),
        }
      );
    },
    change(item, e) {
      this.form = {
        ...this.form,
        food_id: item.food_id,
        quantity: item.quantity,
      };
      if (parseInt(e.target.value) === 0) {
        this.deleteRow(item);
      } else {
        this.form.put(
          this.route("inventories.update", { food_id: item.food_id }),
          { onSuccess: () => (this.updateError = "") }
        );
      }
    },
  },
};
</script>

<style lang="scss" scoped>
</style>