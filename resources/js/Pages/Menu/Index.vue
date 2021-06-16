<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Manage Menu
      </h2>
    </template>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- SEARCH MENU ITEM FORM -->
        <!-- <jet-form-section @submitted="searchSubmit">
          <template #title> Search for menu item </template>
          <template #description>
            Search form allow users easy to find the menu item they already know
            some information.
          </template>
          <template #form>
            <div class="col-span-6 sm:col-span-4">
              <jet-label for="name" value="Name" />
              <jet-input
                id="name"
                type="text"
                class="mt-1 block w-full"
                v-model="form.name"
                ref="name"
                autocomplete="name"
              />
              <jet-input-error
                v-if="form.errors.name"
                :message="form.errors.name"
                class="mt-2"
              />
            </div>
            <div class="col-span-6 sm:col-span-4">
              <jet-label for="type" value="Type" />
              <jet-select v-model="form.type" ref="type" :options="types" />
              <jet-input-error
                v-if="form.errors.type"
                :message="form.errors.type"
                class="mt-2"
              />
            </div>
          </template>
          <template #actions>
            <jet-secondary-button class="mr-2" @click="resetSearch">
              Reset
            </jet-secondary-button>
            <jet-button
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
            >
              Search
            </jet-button>
          </template>
        </jet-form-section> -->

        <!-- CREATE NEW ITEM ACTION -->
        <jet-primary-button @click="show = true" class="my-3 mx-4 sm:mx-0">
          Them mon an
        </jet-primary-button>

        <jet-action-message :on="form.recentlySuccessful" class="mr-3">
          <span class="text-green-500">{{ $page.props.flash.message }}</span>
        </jet-action-message>

        <div class="flex mt-5 flex-wrap px-4 sm:px-0">
          <div
            class="mr-3 mt-3 py-3 px-5 bg-green-500 hover:bg-yellow-400 hover:text-white capitalize cursor-pointer"
            v-for="(menu, k) in menus"
            :key="k"
            @click="handleClick($event, menu)"
          >
            {{ menu.name }}
          </div>
        </div>

        <!-- CREATE MENU ITEM DIALOG FORM -->
        <jet-dialog-modal :show="show" @close="closeModal">
          <template #title>
            <div class="text-lg">{{ formTitle }}</div>
          </template>
          <template #content>
            <div class="mb-4">
              <jet-label for="name" value="Name" />
              <jet-input
                id="type"
                type="text"
                class="mt-1 block w-full"
                v-model="form.name"
                ref="name"
                autocomplete="name"
              />
              <jet-input-error
                v-if="form.errors.name"
                :message="form.errors.name"
                class="mt-2"
              />
            </div>
            <div class="mb-4">
              <jet-label for="typeCb" value="Choose type from exists types." />
              <jet-input
                id="typeCb"
                type="checkbox"
                ref="typeCb"
                @change="typeSelected = !typeSelected"
                :checked="typeSelected"
                autocomplete="typeCb"
              />
            </div>
            <div class="mb-4" v-show="typeSelected == false">
              <jet-label for="type1" value="Type" />
              <jet-input
                id="type1"
                type="text"
                class="mt-1 block w-full"
                v-model="form.type"
                ref="type"
                autocomplete="type"
              />
              <jet-input-error
                v-if="form.errors.type"
                :message="form.errors.type"
                class="mt-2"
              />
            </div>
            <div class="mb-4" v-show="typeSelected == true">
              <jet-label for="type" value="Type" />
              <jet-select v-model="form.type" ref="type" :options="types" />
              <jet-input-error
                v-if="form.errors.type"
                :message="form.errors.type"
                class="mt-2"
              />
            </div>
            <div class="mb-4">
              <jet-label for="price" value="Price" />
              <jet-input
                id="price"
                type="number"
                class="mt-1 block w-full"
                v-model="form.price"
                ref="price"
                autocomplete="price"
                min="0"
                max="500"
              />
              <jet-input-error
                v-if="form.errors.price"
                :message="form.errors.price"
                class="mt-2"
              />
            </div>
          </template>
          <template #footer>
            <jet-primary-button
              @click="save"
              v-if="!editMode"
              :disabled="form.processing"
              class="mr-2"
            >
              Them
            </jet-primary-button>
            <!-- <jet-primary-button
              @click="update"
              v-if="editMode"
              :disabled="form.processing"
              class="mr-2"
            >
              Update
            </jet-primary-button> -->
            <jet-secondary-button @click="closeModal">
              Huy
            </jet-secondary-button>
          </template>
        </jet-dialog-modal>

        <!-- DELETE DIALOG CONFIRM -->
        <jet-dialog-modal :show="confirmDelete" @close="closeModal">
          <template #title> Delete item </template>

          <template #content>
            Are you sure you want to delete this item? Once this item is
            deleted, all of its resources and data will be permanently deleted.
          </template>

          <template #footer>
            <jet-secondary-button @click="confirmDelete = false">
              Huy
            </jet-secondary-button>
            <jet-danger-button
              class="ml-2"
              @click.native="deleteMenuItem"
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
import JetSecondaryButton from "../../Jetstream/SecondaryButton.vue";
import JetDangerButton from "@/Jetstream/DangerButton";
import JetActionMessage from "@/Jetstream/ActionMessage";
const INIT_MENU = {
  food_id: "",
  name: "",
  type: "",
  price: "",
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
  props: {
    menus: Array,
    types: Array,
  },
  data() {
    return {
      show: false,
      form: this.$inertia.form(INIT_MENU, {
        bag: "createMenu",
        resetOnSuccess: true,
      }),
      editMode: false,
      confirmDelete: false,
      typeSelected: false,
      formTitle: FORM_TITLE.create,
      delay: 700,
      clicks: 0,
      timer: null,
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
    deleteItem(menu) {
      this.confirmDelete = true;
      this.form = { ...this.form, id: menu.food_id };
    },
    deleteMenuItem() {
      this.form.delete(this.route("menus.destroy", { menu: this.form }), {
        onSuccess: () => {
          this.confirmDelete = false;
        },
      });
    },
    save() {
      this.form.post(this.route("menus.store"), {
        onSuccess: () => {
          this.closeModal();
        },
        onError: () => {
          this.show = true;
        },
      });
    },
    update() {
      this.form.put(
        this.route("menus.update", {
          menu: { ...this.form, id: this.form.food_id },
        }),
        {
          onSuccess: () => this.closeModal(),
        }
      );
    },
    resetSearch() {
      this.form.reset();
      this.form.get(this.route("menus.index"));
    },
    searchSubmit() {
      this.$inertia.get(
        this.route("menus.search", {
          name: this.form.name,
          type: this.form.type,
        })
      );
    },
    edit(menu) {
      this.show = true;
      this.editMode = true;
      this.formTitle = FORM_TITLE.edit;
      this.form = { ...this.form, ...menu };
    },
    handleClick(event, menu) {
      this.clicks++;
      if (this.clicks === 1) {
        var self = this;
        this.deleteItem(menu);
        this.clicks = 0;
      }
    },
  },
};
</script>

<style lang="scss" scoped>
</style>