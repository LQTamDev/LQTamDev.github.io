<template>
  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <h1 class="text-center font-bold">
        Paycheck for {{ employee.fname }} {{ employee.lname }}
      </h1>

      <div class="bg-white overflow-x-auto shadow-xl sm:rounded-lg p-4">
        <table id="table" class="w-full">
          <thead>
            <tr class="bg-gray-100">
              <th class="px-4 py-2">Date</th>
              <!-- <th class="px-4 py-2">From</th>
              <th class="px-4 py-2">To</th> -->
              <th class="px-4 py-2">Hours</th>
              <th class="px-4 py-2">Hour Wage</th>
              <!-- <th class="px-4 py-2">Comments</th>
              <th class="px-4 py-2">Submitted</th> -->
            </tr>
          </thead>
          <tbody>
            <template v-for="row in employee.timesheet" :key="row.id">
              <tr>
                <td class="border px-4 py-2">
                  {{ row.date }}
                </td>
                <!-- <td class="border px-4 py-2">
                  {{ row.time_from }}
                </td>
                <td class="border px-4 py-2">
                  {{ row.time_to }}
                </td> -->
                <td class="border px-4 py-2">
                  {{ row.hours }}
                </td>
                <td class="border px-4 py-2">
                  {{ employee.wage }}
                </td>
                <!-- <td class="border px-4 py-2">
                  {{ row.comments }}
                </td>
                <td class="border px-4 py-2">
                  {{ row.created_at }}
                </td> -->
              </tr>
            </template>
            <tr>
              <td class="border p-4 text-center font-bold" colspan="12">
                Total: {{ employeeTotalWorked(employee.timesheet) }} hours
                worked
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    employee: {
      type: Array,
      default: [],
    },
  },
  mounted() {
    window.print();
    window.onafterprint = () => window.history.back();
  },
  methods: {
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
  },
};
</script>
