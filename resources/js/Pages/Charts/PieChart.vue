<script>
import { Pie } from "vue3-chart-v2";

export default {
  extends: Pie,
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
    this.gradient = this.$refs.canvas
      .getContext("2d")
      .createLinearGradient(0, 0, 0, 450);
    this.gradient2 = this.$refs.canvas
      .getContext("2d")
      .createLinearGradient(0, 0, 0, 450);

    this.gradient.addColorStop(0, "rgba(255, 0,0, 0.5)");
    this.gradient.addColorStop(0.5, "rgba(255, 0, 0, 0.25)");
    this.gradient.addColorStop(1, "rgba(255, 0, 0, 0)");

    this.gradient2.addColorStop(0, "rgba(0, 231, 255, 0.9)");
    this.gradient2.addColorStop(0.5, "rgba(0, 231, 255, 0.25)");
    this.gradient2.addColorStop(1, "rgba(0, 231, 255, 0)");

    this.renderChart(
      {
        labels: this.data.labels,
        datasets: [
          {
            backgroundColor: this.data.backgroundColor,
            data: this.data.data,
          },
        ],
      },
      {
        title: {
          display: true,
          text: "Popular Item",
        },
        responsive: true,
        maintainAspectRatio: false,
      }
    );
  },
};
</script>