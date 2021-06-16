<script>
import { Bar } from "vue3-chart-v2";

export default {
  extends: Bar,
  props: {
    data: {
      type: Object,
      required: true,
      default: {},
    },
    options: {
      type: Object,
      required: true,
      default: {},
    },
  },
  mounted() {
    this.renderChart(
      {
        labels: this.data.labels,
        datasets: this.data.dataset,
      },
      {
        title: {
          display: true,
          text: "Employee Efficient",
        },
        responsive: true,
        maintainAspectRatio: false,
      }
    );
  },
};
</script>