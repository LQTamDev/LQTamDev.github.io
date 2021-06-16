<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Manage Employee
      </h2>
    </template>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-x-auto shadow-xl sm:rounded-lg px-4 py-4">
          <jet-action-message :on="form.recentlySuccessful" class="mr-3">
            <span class="text-green-500">{{ $page.props.flash.message }}</span>
          </jet-action-message>
          <jet-primary-button @click="show = true" class="my-3">
            Them nhan vien
          </jet-primary-button>
          <!-- <export-excel :data="employees">
            Download Data
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
              />
            </svg>
          </export-excel> -->
          <!-- <download-csv :data="employees">
            Download Data
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
              />
            </svg>
          </download-csv> -->
          <table class="w-full">
            <thead>
              <tr class="bg-gray-100">
                <th class="px-4 py-2 w-20">No.</th>
                <th class="px-4 py-2">Full Name</th>
                <th class="px-4 py-2">Email</th>
                <th class="px-4 py-2">Wage</th>
                <th class="px-4 py-2">Role</th>
                <th class="px-4 py-2">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="row in employees" :key="row.id">
                <td class="border px-4 py-2">{{ row.id }}</td>
                <td class="border px-4 py-2">{{ row.name }}</td>
                <td class="border px-4 py-2">{{ row.email }}</td>
                <td class="border px-4 py-2">{{ row.wage }}</td>
                <td class="border px-4 py-2">
                  {{ row.role }}
                </td>
                <td class="border px-4 py-2">
                  <!-- <jet-success-button @click="payCheck(row)">
                    Paycheck
                  </jet-success-button> -->
                  <jet-primary-button @click="edit(row)">
                    Sua
                  </jet-primary-button>
                  <jet-danger-button @click="deleteRow(row)">
                    Xoa
                  </jet-danger-button>
                </td>
              </tr>
            </tbody>
          </table>
          <jet-dialog-modal :show="show" @close="closeModal">
            <template #title>
              <div class="text-lg">{{ formTitle }}</div>
            </template>
            <template #content>
              <div class="mb-4">
                <jet-label for="role" value="Role" />
                <select
                  class="capitalize block w-full pl-3 pr-10 py-2 transition duration-100 ease-in-out border border-gray-300 rounded shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none focus:ring-opacity-50 disabled:opacity-50 disabled:cursor-not-allowed"
                  v-model="form.role"
                  ref="role"
                >
                  <option value="">Please select...</option>
                  <option
                    v-for="(opt, k) in roleArray"
                    :key="k"
                    :value="opt.id ?? opt.name"
                    :selected="form.role === opt.name"
                  >
                    {{ opt.name }}
                  </option>
                </select>
                <jet-input-error
                  v-if="form.errors.role"
                  :message="form.errors.role"
                  class="mt-2"
                />
              </div>
              <div class="mb-4">
                <jet-label for="ssn" value="SSN" />
                <jet-input
                  id="ssn"
                  type="number"
                  class="mt-1 block w-full"
                  v-model="form.ssn"
                  ref="ssn"
                  autocomplete="ssn"
                  min="0"
                  max="999999999"
                />
                <jet-input-error
                  v-if="form.errors.ssn"
                  :message="form.errors.ssn"
                  class="mt-2"
                />
              </div>
              <div class="mb-4">
                <jet-label for="fname" value="First Name" />
                <jet-input
                  id="fname"
                  type="text"
                  class="mt-1 block w-full"
                  v-model="form.fname"
                  ref="fname"
                  autocomplete="fname"
                />
                <jet-input-error
                  v-if="form.errors.fname"
                  :message="form.errors.fname"
                  class="mt-2"
                />
              </div>
              <div class="mb-4">
                <jet-label for="lname" value="Last Name" />
                <jet-input
                  id="lname"
                  type="text"
                  class="mt-1 block w-full"
                  v-model="form.lname"
                  ref="lname"
                />
                <jet-input-error
                  v-if="form.errors.lname"
                  :message="form.errors.lname"
                  class="mt-2"
                />
              </div>
              <div class="mb-4">
                <jet-label for="wage" value="Wage" />
                <jet-input
                  id="wage"
                  type="number"
                  class="mt-1 block w-full"
                  v-model="form.wage"
                  ref="wage"
                  min="0"
                  max="1000000"
                />
                <jet-input-error
                  v-if="form.errors.wage"
                  :message="form.errors.wage"
                  class="mt-2"
                />
              </div>
              <div class="mb-4">
                <jet-label for="email" value="Email" />
                <jet-input
                  id="email"
                  type="email"
                  class="mt-1 block w-full"
                  v-model="form.email"
                  ref="email"
                />
                <jet-input-error
                  v-if="form.errors.email"
                  :message="form.errors.email"
                  class="mt-2"
                />
              </div>
              <div class="mb-4" v-if="!editMode">
                <jet-label for="password" value="Password" />
                <jet-input
                  id="password"
                  type="password"
                  class="mt-1 block w-full"
                  v-model="form.password"
                  ref="password"
                />
                <jet-input-error
                  v-if="form.errors.password"
                  :message="form.errors.password"
                  class="mt-2"
                />
              </div>
              <div class="mb-4" v-if="!editMode && form.password">
                <jet-label
                  for="password_confirmation"
                  value="Password Confirmation"
                />
                <jet-input
                  id="password_confirmation"
                  type="password"
                  class="mt-1 block w-full"
                  v-model="form.password_confirmation"
                  ref="password_confirmation"
                />
                <jet-input-error
                  v-if="form.errors.password_confirmation"
                  :message="form.errors.password_confirmation"
                  class="mt-2"
                />
              </div>
            </template>
            <template #footer>
              <jet-success-button
                v-show="!editMode"
                @click="save(form)"
                :disabled="form.processing"
                class="mr-2"
              >
                Them
              </jet-success-button>
              <jet-success-button
                v-show="editMode"
                :disabled="form.processing"
                @click="save(form)"
                class="mr-2"
              >
                Sua
              </jet-success-button>
              <jet-secondary-button @click="closeModal">
                Huy
              </jet-secondary-button>
            </template>
          </jet-dialog-modal>
          <jet-dialog-modal :show="confirmDelete" @close="closeModal">
            <template #title> Delete employee </template>

            <template #content>
              Are you sure you want to delete this employee? Once this employee
              is deleted, all of its resources and data will be permanently
              deleted.
            </template>

            <template #footer>
              <jet-secondary-button @click="confirmDelete = false">
                Huy
              </jet-secondary-button>
              <jet-danger-button
                class="ml-2"
                @click.native="deleteEmployee"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
              >
                Xoa
              </jet-danger-button>
            </template>
          </jet-dialog-modal>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetPrimaryButton from "@/Jetstream/PrimaryButton.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetSuccessButton from "@/Jetstream/SuccessButton.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import ValidationErrors from "@/Jetstream/ValidationErrors.vue";
import JetActionMessage from "@/Jetstream/ActionMessage.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetSelect from "@/Jetstream/Select.vue";
const INIT_EMPLOYEE = {
  fname: "",
  lname: "",
  name: "",
  wage: "",
  email: "",
  ssn: "",
  password: "",
  password_confirmation: "",
  user_id: "",
  role: "",
};
const FORM_TITLE = {
  create: "Create",
  edit: "Edit",
};
export default {
  components: {
    AppLayout,
    JetDialogModal,
    JetInput,
    JetActionMessage,
    JetInputError,
    JetSecondaryButton,
    ValidationErrors,
    JetInputError,
    JetPrimaryButton,
    JetDangerButton,
    JetSuccessButton,
    JetLabel,
    JetSelect,
  },
  props: {
    employees: Array,
    url: String,
  },
  data() {
    return {
      show: false,
      confirmDelete: false,
      maxWidth: "2xl",
      formTitle: FORM_TITLE.create,
      form: this.$inertia.form(INIT_EMPLOYEE, {
        bag: "createEmployee",
        resetOnSuccess: true,
      }),
      editMode: false,
      roleArray: [],
    };
  },
  methods: {
    edit(employee) {
      this.formTitle = FORM_TITLE.edit;
      this.show = true;
      this.form = {
        ...this.form,
        ssn: employee.ssn,
        fname: employee.fname,
        lname: employee.lname,
        email: employee.email,
        name: employee.name,
        wage: employee.wage,
        id: employee.id,
        user_id: employee.user_id,
        role: employee.role_id,
      };
      this.editMode = true;
    },
    closeModal() {
      this.show = false;
      if (this.form.errors) this.form.clearErrors();
      this.form.reset();
      this.formTitle = FORM_TITLE.create;
      this.editMode = false;
      this.confirmDelete = false;
    },
    save(form) {
      form = form.transform((data) => ({
        ...data,
        ssn: parseInt(data.ssn),
        name: data.fname + " " + data.lname,
        wage: parseInt(data.wage),
      }));
      if (!this.editMode) {
        form.post(this.route("users.store"), {
          onSuccess: () => {
            this.closeModal();
          },
          onError: () => {
            this.show = true;
          },
        });
      } else {
        form.put(
          this.route("users.update", {
            user: { ...form, id: form.user_id },
          }),
          {
            onSuccess: () => {
              this.closeModal();
            },
            onError: () => {
              this.show = true;
            },
          }
        );
      }
    },
    deleteEmployee() {
      this.form.delete(this.route("users.destroy", { user: this.form }), {
        onSuccess: () => {
          this.confirmDelete = false;
        },
      });
    },
    deleteRow(employee = {}) {
      this.form = { ...this.form, id: employee.user_id };
      this.confirmDelete = true;
    },
    payCheck(employee) {
      this.$inertia.get(route("employees.show", employee.id));
    },
  },
  mounted() {
    fetch(this.route("roles.index"))
      .then((res) => res.json())
      .then((data) => (this.roleArray = data));
  },
};
</script>
