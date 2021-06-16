<template>
  <app-layout>
    <div class="bg-gradient-to-r from-purple-300 to-blue-200">
      <div
        class="w-9/12 m-auto py-16 min-h-screen flex items-center justify-center"
      >
        <div class="bg-white shadow overflow-hidden sm:rounded-lg pb-8">
          <div class="border-t border-gray-200 text-center pt-8">
            <h1 class="text-9xl font-bold text-purple-400">{{ title }}</h1>
            <h1 class="text-6xl font-medium py-8">oops!</h1>
            <p class="text-2xl pb-8 px-12 font-medium">{{ description }}</p>
            <button
              onclick="window.history.back()"
              class="bg-gradient-to-r from-purple-400 to-blue-500 hover:from-pink-500 hover:to-orange-500 text-white font-semibold px-6 py-3 rounded-md mr-6"
            >
              Back to previous
            </button>
          </div>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "../Layouts/AppLayout.vue";
export default {
  components: { AppLayout },

  props: {
    status: Number,
  },
  computed: {
    title() {
      return {
        503: "503: Service Unavailable",
        500: "500: Server Error",
        404: "404: Page Not Found",
        403: "403: Forbidden",
      }[this.status];
    },
    description() {
      return {
        503: "Sorry, we are doing some maintenance. Please check back soon.",
        500: "Whoops, something went wrong on our servers.",
        404: "Sorry, the page you are looking for could not be found.",
        403: "Sorry, you are forbidden from accessing this page.",
      }[this.status];
    },
  },
};
</script>