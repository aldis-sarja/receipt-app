<template>
  <div>
    <NavBar />

    <div class="frame">
      <div class="add-receipt">
        <span class="error-section" v-if="validationErrors">
          {{ this.validationErrors }}
        </span>

        <form
          class="add-receipt-section"
          ref="formName"
          @submit.prevent="onCreateReceipt"
        >
          <input class="button" type="submit" value="New Receipt" />

          <div class="input-section">
            <ul>
              <li v-for="item in items" v-bind:key="item">
                <input
                  class="checkbox"
                  :id="item.id"
                  :value="item.id"
                  type="checkbox"
                  v-model="checkedItems"
                />
                <label :for="item.id">{{ item.name }}</label>
              </li>
            </ul>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "NewReceipt",

  data() {
    return {
      items: [],
      checkedItems: [],
      receipt: [],

      validationErrors: null,
    };
  },

  created() {
    this.getItems();
  },

  methods: {
    async getItems() {
      const payload = {
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
          "Access-Control-Allow-Origin": "*",
        },
      };
      try {
        const res = await axios.get(
          this.$config.BACKEND_URL + "/items",
          payload
        );
        this.items = res.data;
      } catch (error) {
        this.validationErrors = error.response.data;
      }
    },

    async createReceipt() {
      const items = "[" + this.checkedItems.join(",") + "]";
      const payload = {
        items: items,
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
          "Access-Control-Allow-Origin": "*",
        },
      };

      try {
        const res = await axios.post(
          this.$config.BACKEND_URL + "/receipts",
          payload
        );
        this.receipt = res.data;
        this.$router.push("/receipt/" + this.receipt.id);
      } catch (error) {
        this.validationErrors = error.response.data;
      }
    },

    onCreateReceipt() {
      if (!this.checkedItems.length) {
        this.validationErrors = "Choose at least one item!";
        return;
      }

      this.createReceipt();
    },
  },
};
</script>

<style scoped>
.frame {
  margin-top: 30px;
  margin-left: 20px;
  display: flex;
  flex-direction: column;
}

.error-section {
  color: red;
  margin-top: 20px;
  font-size: 12px;
}
</style>