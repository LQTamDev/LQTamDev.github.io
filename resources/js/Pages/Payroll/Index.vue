<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Payroll</h2>
    </template>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-x-auto shadow-xl sm:rounded-lg p-4">
          <action-message :on="form.recentlySuccessful" class="mr-3">
            <span class="text-green-500">{{ $page.props.flash.message }}</span>
          </action-message>
          <div>Click + to add the start and end time for the day's hours:</div>
          <div class="w-full pb-3">
            <jet-secondary-button @click="show = true">
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
                  d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </jet-secondary-button>
          </div>
          <table class="w-full">
            <thead>
              <tr class="bg-gray-100">
                <th class="px-4 py-2 w-20">Employee</th>
                <th class="px-4 py-2">Date</th>
                <th class="px-4 py-2">Time_From</th>
                <th class="px-4 py-2">Time_To</th>
                <th class="px-4 py-2">Hours</th>
                <th class="px-4 py-2">Hour Wage</th>
                <th class="px-4 py-2">Comments</th>
                <th class="px-4 py-2">Date_Submitted</th>
              </tr>
            </thead>
            <tbody>
              <template v-for="row in employeesComp" :key="row.id">
                <tr
                  :class="`employee-${row.id}`"
                  @click="toggleView(row, $event)"
                >
                  <td class="border p-4" colspan="9">
                    <span class="inline-block cursor-pointer">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="border h-3 w-3"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                        />
                      </svg>
                    </span>
                    {{ row.name }}
                  </td>
                </tr>
                <template v-for="time in row.timesheet" :key="time.id">
                  <tr
                    @click="editRecord(time)"
                    :class="`employee-${row.id} time-${row.id} hidden`"
                  >
                    <td class="border px-4 py-2">
                      {{ row.name }}
                    </td>
                    <td class="border px-4 py-2">
                      {{ time.date }}
                    </td>
                    <td class="border px-4 py-2">
                      {{ time.time_from }}
                    </td>
                    <td class="border px-4 py-2">
                      {{ time.time_to }}
                    </td>
                    <td class="border px-4 py-2">
                      {{ time.hours }}
                    </td>
                    <td class="border px-4 py-2">
                      {{ row.wage }}
                    </td>
                    <td class="border px-4 py-2">
                      {{ time.comments }}
                    </td>
                    <td class="border px-4 py-2">
                      {{ time.created_at }}
                    </td>
                  </tr>
                </template>

                <tr v-if="row.timesheet.length > 0">
                  <td class="border px-4 py-2 text-center" colspan="6">
                    {{ employeeTotalWorked(row.timesheet) }}
                  </td>
                  <td class="border px-4 py-2">
                    <a
                      @click="payCheck(row)"
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
                      Lap phieu luong
                    </a>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>

          <jet-dialog-modal>
            <template #content>
              <table id="table" class="w-full">
                <thead>
                  <tr class="bg-gray-100">
                    <th class="px-4 py-2">Date</th>
                    <th class="px-4 py-2">Time_From</th>
                    <th class="px-4 py-2">Time_To</th>
                    <th class="px-4 py-2">Hours</th>
                    <th class="px-4 py-2">Hour Wage</th>
                    <th class="px-4 py-2">Comments</th>
                    <th class="px-4 py-2">Date_Submitted</th>
                  </tr>
                </thead>
                <tbody>
                  <template
                    v-for="row in emSelectedComp.timesheet"
                    :key="row.id"
                  >
                    <tr
                      @click="editRecord(row)"
                      :class="`employee-${row.id} time-${row.id}`"
                    >
                      <td class="border px-4 py-2">
                        {{ row.date }}
                      </td>
                      <td class="border px-4 py-2">
                        {{ row.time_from }}
                      </td>
                      <td class="border px-4 py-2">
                        {{ row.time_to }}
                      </td>
                      <td class="border px-4 py-2">
                        {{ row.hours }}
                      </td>
                      <td class="border px-4 py-2">
                        {{ emSelectedComp.wage }}
                      </td>
                      <td class="border px-4 py-2">
                        {{ row.comments }}
                      </td>
                      <td class="border px-4 py-2">
                        {{ row.created_at }}
                      </td>
                    </tr>
                  </template>
                  <!-- <tr v-if="employeeComp.length > 0"> -->
                  <tr>
                    <td class="border p-4 text-center font-bold" colspan="5">
                      {{ employeeTotalWorked(emSelectedComp.timesheet) }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </template>
          </jet-dialog-modal>
          <!-- CREATE NEW TIME -->
          <jet-dialog-modal :show="show" @close="closeModal">
            <template #title>
              <div class="text-lg bg-gray-100 border-b-4 p-2">
                {{ formTitle }} Record
              </div>
            </template>
            <template #content>
              <div class="mb-4" v-show="isManager">
                <jet-label for="employee" value="Employee" />
                <select
                  class="
                    capitalize
                    block
                    w-full
                    pl-3
                    pr-10
                    py-2
                    transition
                    duration-100
                    ease-in-out
                    border border-gray-300
                    rounded
                    shadow-sm
                    focus:ring-2 focus:ring-blue-500
                    focus:outline-none
                    focus:ring-opacity-50
                    disabled:opacity-50
                    disabled:cursor-not-allowed
                  "
                  v-model="form.employee_id"
                  ref="employee"
                >
                  <option value="" selected>Please select...</option>
                  <option v-for="(em, k) in employees" :key="k" :value="em.id">
                    {{ em.name }}
                  </option>
                </select>
                <jet-input-error
                  v-if="form.errors.employee_id"
                  :message="form.errors.employee_id"
                  class="mt-2"
                />
              </div>
              <div class="mb-4">
                <jet-label for="date" value="Date" />
                <jet-input
                  id="date"
                  type="date"
                  class="mt-1 w-full"
                  v-model="form.date"
                  ref="date"
                  autocomplete="date"
                  autofocus="date"
                />
                <jet-input-error
                  v-if="form.errors.date"
                  :message="form.errors.date"
                  class="mt-2"
                />
              </div>
              <div class="mb-4">
                <jet-label for="time_from" value="Time From" />
                <jet-input
                  id="time_from"
                  type="time"
                  class="mt-1 w-full"
                  v-model="form.time_from"
                  ref="time_from"
                  autocomplete="time_from"
                  autofocus="time_from"
                />
                <jet-input-error
                  v-if="form.errors.time_from"
                  :message="form.errors.time_from"
                  class="mt-2"
                />
              </div>
              <div class="mb-4">
                <jet-label for="time_to" value="Time To" />
                <jet-input
                  id="time_to"
                  type="time"
                  class="mt-1 w-full"
                  v-model="form.time_to"
                  ref="time_to"
                  autocomplete="time_to"
                  autofocus="time_to"
                />
                <jet-input-error
                  v-if="form.errors.time_to"
                  :message="form.errors.time_to"
                  class="mt-2"
                />
              </div>
              <!-- <div class="mb-4" v-if="employeeTimeSheetsComp.length > 0">
                <jet-label for="pay_rate" value="Hour Type" />
                <select
                  class="capitalize block w-full pl-3 pr-10 py-2 transition duration-100 ease-in-out border border-gray-300 rounded shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none focus:ring-opacity-50 disabled:opacity-50 disabled:cursor-not-allowed"
                  v-model="form.hour_type"
                  ref="pay_rate"
                  @change="hourTypeChange"
                >
                  <option
                    v-for="(type, k) in employeeTimeSheetsComp[0].pay_rate"
                    :key="k"
                    :value="type.id"
                    :selected="form.hour_type === type.id"
                  >
                    {{ type.type }}
                  </option>
                </select>
                <jet-input-error
                  v-if="form.errors.pay_rate"
                  :message="form.errors.pay_rate"
                  class="mt-2"
                />
              </div>
              <div class="mb-4" v-if="form.hour_type">
                <jet-label for="hour_pay" value="Hour Cost" />
                <div>
                  {{ hourCostCalculator }}
                </div>
              </div> -->
              <div class="mb-4">
                <jet-label for="comments" value="Comments" />
                <textarea
                  id="comments"
                  class="
                    border-gray-300
                    focus:border-indigo-300
                    focus:ring focus:ring-indigo-200 focus:ring-opacity-50
                    rounded-md
                    shadow-sm
                    mt-1
                    w-full
                  "
                  v-model="form.comments"
                  ref="comments"
                  autocomplete="comments"
                  autofocus="comments"
                ></textarea>
                <jet-input-error
                  v-if="form.errors.comments"
                  :message="form.errors.comments"
                  class="mt-2"
                />
              </div>
            </template>
            <template #footer>
              <jet-success-button
                v-show="!editMode"
                @click="save"
                :disabled="form.processing"
                class="mr-2"
              >
                Save
              </jet-success-button>
              <jet-success-button
                v-show="editMode"
                :disabled="form.processing"
                @click="save"
                class="mr-2"
              >
                Update
              </jet-success-button>
              <jet-secondary-button @click="closeModal">
                Cancel
              </jet-secondary-button>
            </template>
          </jet-dialog-modal>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import ActionMessage from "../../Jetstream/ActionMessage.vue";
import JetSuccessButton from "../../Jetstream/SuccessButton.vue";
import JetPrimaryButton from "../../Jetstream/PrimaryButton.vue";
import JetSecondaryButton from "../../Jetstream/SecondaryButton.vue";
import JetDialogModal from "../../Jetstream/DialogModal.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import moment from "moment";
const INIT = {
  date: null,
  id: null,
  time_from: null,
  time_to: null,
  comments: null,
  employee_id: null,
  pay_rate: {},
  hour_type: null,
  totalWorked: 0,
};
const FORM_TITLE = {
  add: "Add",
  edit: "Edit",
};
export default {
  components: {
    AppLayout,
    ActionMessage,
    JetSuccessButton,
    JetPrimaryButton,
    JetSecondaryButton,
    JetDialogModal,
    JetLabel,
    JetInput,
    JetInputError,
  },
  props: ["employees"],
  data() {
    return {
      show: false,
      formTitle: FORM_TITLE.add,
      confirmDelete: false,
      editMode: false,
      form: this.$inertia.form(INIT, { resetOnSuccess: false }),
      emSelected: { timesheet: [] },
    };
  },
  computed: {
    emSelectedComp() {
      return this.emSelected;
    },
    isManager() {
      return this.$root.roleArrayComputed.some((role) => role === "manager");
    },
    employeesComp() {
      return this.employees;
    },
    hourCostCalculator() {
      return this.form.pay_rate.filter((rate) => rate.selected === true)[0]
        .pay_rate;
    },
  },
  methods: {
    async print() {
      const printOptions = {
        styles: ["https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css"],
      };
      let el = document.querySelector("#table");

      let cloned = el.cloneNode(true);
      let section = document.getElementById("print");

      if (!section) {
        section = document.createElement("div");
        section.id = "print";
        document.body.appendChild(section);
      }

      section.innerHTML = "";
      section.appendChild(cloned);
      await this.$htmlToPaper(section.id, printOptions);
    },
    hourTypeChange() {
      this.employeeTimeSheetsComp[0].pay_rate.forEach((rate) =>
        rate.id === this.form.hour_type
          ? (rate.selected = true)
          : (rate.selected = false)
      );
      this.form.pay_rate = this.employeeTimeSheetsComp[0].pay_rate;
    },
    employeeTotalWorked(emTimeSheets) {
      var totalHour = 0;
      var totalMinute = 0;
      emTimeSheets.forEach((e) => {
        totalHour += Math.floor(parseInt(e.hours));
        let num = parseInt(e.hours.toString().split(":")[1]);
        if (!isNaN(num)) totalMinute += num;
      });
      totalHour =
        totalHour + Math.floor(totalMinute / 60) + ":" + (totalMinute % 60);
      return totalHour;
    },
    closeModal() {
      this.show = false;
      if (this.form.errors) this.form.clearErrors();
      this.form.reset();
      this.formTitle = FORM_TITLE.add;
      this.editMode = false;
      this.confirmDelete = false;
    },
    toggleView(row, event) {
      var childTime = document.querySelectorAll(`.time-${row.id}`);
      childTime.forEach((child) => child.classList.toggle("hidden"));
      var target = event.target;
      if (event.target.tagName !== "path") {
        target = target.querySelector("path");
      }
      target.getAttribute("d").includes("M12 6v6m0 0v6m0-6h6m-6 0H6")
        ? target.setAttribute("d", "M20 12H4")
        : target.setAttribute("d", "M12 6v6m0 0v6m0-6h6m-6 0H6");
    },
    save() {
      if (this.editMode) {
        this.form.put(
          this.route("employee.timesheet.update", {
            id: this.form.id,
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
      } else {
        if (this.form.employee_id === null) {
          this.form.employee_id = this.$page.props.employee.id;
        }
        this.form.post(this.route("employee.timesheet.store"), {
          onSuccess: () => {
            this.closeModal();
          },
          onError: () => {
            this.show = true;
          },
        });
      }
    },
    async payCheck(row = {}) {
      this.emSelected = row;
      row.totalWorked = this.employeeTotalWorked(row.timesheet).replace(
        ":",
        "."
      );
      this.form.totalWorked = row.totalWorked;
      this.form.id = row.id;
      await this.$inertia.get(
        this.route("employees.paycheck.show", row.user_id)
      );
      this.form.post(this.route("employees.paycheck", row), row);
    },
    editRecord(time) {
      this.editMode = true;
      this.formTitle = FORM_TITLE.edit;
      this.form = {
        ...this.form,
        id: time.id,
        date: time.date.split(" ")[0],
        time_from: moment(time.time_from, "hh:mm A").format("HH:mm"),
        time_to: moment(time.time_to, "hh:mm A").format("HH:mm"),
        comments: time.comments,
        employee_id: time.employee_id,
      };
      this.show = true;
    },
  },
};
</script>
