<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Statistical
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex mb-4">
          <jet-input
            id="date_from"
            type="date"
            class="mt-1 mr-2"
            v-model="form.date_from"
            ref="date_from"
            autocomplete="date_from"
            autofocus="date_from"
          />
          <jet-input
            id="date_to"
            type="date"
            class="mt-1 mr-2"
            v-model="form.date_to"
            ref="date_to"
            autocomplete="date_to"
          />
          <jet-primary-button class="mt-1" @click="generateGraph">
            Submit
          </jet-primary-button>
        </div>
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <bar-chart
            :data="graphs.employeeEfficient"
            :options="barOptions"
          ></bar-chart>
          <line-chart
            :data="graphs.restaurantTraffic"
            :options="lineOptions"
          ></line-chart>
          <pie-chart
            :data="graphs.popularItem"
            :options="pieOptions"
          ></pie-chart>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import BarChart from "./Charts/BarChart.vue";
import PieChart from "./Charts/PieChart.vue";
import LineChart from "./Charts/LineChart.vue";
import JetInput from "../Jetstream/Input.vue";
import JetPrimaryButton from "../Jetstream/PrimaryButton";
export default {
  components: {
    AppLayout,
    LineChart,
    PieChart,
    BarChart,
    JetInput,
    JetPrimaryButton,
  },
  props: ["graphs"],
  data() {
    return {
      form: this.$inertia.form({ date_from: null, date_to: null }),
      lineGradient1: null,
      lineGradient2: null,
      pieGradient1: null,
      pieGradient2: null,
      barData: {},
      barOptions: {},
      pieData: {},
      pieOptions: {},
      lineData: {},
      lineOptions: {},
    };
  },
  methods: {
    generateGraph() {
      this.form.get(this.route("dashboard"));
    },
  },
};
</script>
