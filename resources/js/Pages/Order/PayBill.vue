<template>
  <div class="max-w-xs m-auto">
    <h1 class="text-center font-bold">Restaurant Bill</h1>
    <div class="flex justify-between">
      <div>
        {{ `${orders[0].table.waiter.fname} ${orders[0].table.waiter.lname}` }}
      </div>
      <div>
        {{ new Date(orders[orders.length - 1].updated_at).toLocaleString() }}
      </div>
    </div>
    <hr class="mt-5 border-gray-500 border-t-dotted border-t-2" />
    <p>Payment</p>
    <table class="w-full text-center">
      <thead>
        <tr>
          <th>No.</th>
          <th>Name</th>
          <th>Type</th>
          <th>Price</th>
        </tr>
      </thead>
      <tbody>
        <template v-for="(foodArr, key) in orders" :key="key">
          <tr v-for="(food, k) in JSON.parse(foodArr.data)" :key="k">
            <td>{{ key }}</td>
            <td>{{ food.name }}</td>
            <td>{{ food.type }}</td>
            <td>{{ food.price }}</td>
          </tr>
        </template>
      </tbody>
    </table>
    <hr class="my-5 border-gray-500 border-t-dotted border-t-2" />
    <div class="flex justify-center">
      <div>
        {{ `Total: ` }}
      </div>
      <div class="pl-2 font-bold">
        {{
          orders.reduce((accumulator, foodArr) => {
            return (
              accumulator +
              JSON.parse(foodArr.data).reduce((accumulator1, currentValue) => {
                return accumulator1 + currentValue.price;
              }, 0)
            );
          }, 0)
        }}
      </div>
    </div>
    <div class="text-center py-5">Thank you!</div>
  </div>
</template>

<script>
export default {
  props: {
    orders: {
      type: Array,
      required: true,
    },
  },
  mounted() {
    window.print();
    window.onafterprint = () => this.$inertia.visit(this.route("tables.index"));
  },
};
</script>