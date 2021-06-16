<template>
  <select
    class="capitalize block w-full pl-3 pr-10 py-2 transition duration-100 ease-in-out border border-gray-300 rounded shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none focus:ring-opacity-50 disabled:opacity-50 disabled:cursor-not-allowed"
    :value="modelValue"
    @input="$emit('update:modelValue', $event.target.value)"
    ref="select"
  >
    <option value="">Please select...</option>
    <option v-for="(opt, k) in options" :key="k" :value="opt.id ?? opt.name">
      {{ opt.name }}
    </option>
  </select>
</template>

<script>
export default {
  props: ["modelValue", "options"],

  emits: ["update:modelValue"],

  methods: {
    focus() {
      this.$refs.select.focus();
    },
  },
};
</script>

