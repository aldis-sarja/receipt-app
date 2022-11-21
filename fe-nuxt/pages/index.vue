<template>
  <div>
    <NavBar />
    <div class="frame">
      <h2>Receipts</h2>

      <nuxt-link :to="'/new-receipt'"> Add new Receipt </nuxt-link>

      <span class="error-section" v-if="validationErrors">
        {{ this.validationErrors }}
      </span>

      <div class="filters-section">
        <span class="filter-label">Date Range:</span>
        <input type="date" v-model="startDate" required /> -
        <input type="date" v-model="endDate" required />
        <span class="filter-label">Item Name:</span>
        <input type="text" v-model="item" />
        <button class="filter-button" v-on:click="onFilter">Filter</button>
        <button class="filter-button" v-on:click="onClear">Clear</button>
      </div>
      <ul>
        <li class="receipt" v-for="receipt in receipts" v-bind:key="receipt">
          <nuxt-link :to="'/receipt/' + receipt.id">
            Receipt: {{ receipt.created_at }}
          </nuxt-link>
          <ul>
            <li class="item" v-for="item in receipt.items" v-bind:key="item">
              {{ item.name }}
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
import Vue from "vue";
import axios from "axios";

export default {
  name: "IndexPage",

  data() {
    return {
      receipts: [],
      startDate: null,
      endDate: null,
      item: null,
    };
  },

  created() {
    this.getReceipts();
  },

  methods: {
    async getReceipts() {
      const payload = {
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
          "Access-Control-Allow-Origin": "*",
        },
      };
      try {
        const res = await axios.get(
          this.$config.BACKEND_URL + "/receipts",
          payload
        );
        this.receipts = res.data;
        for (const receipt of this.receipts) {
          receipt.created_at = this.convertDateTime(receipt.created_at);
        }
      } catch (error) {
        this.validationErrors = error.response.data;
      }
    },

    async getFilteredReceipts() {
      var filters = "";
      if (this.item) {
        filters += "?items=" + this.item;
      }

      if (this.startDate) {
        filters += "?date=" + this.startDate + "," + this.endDate;
      }

      const payload = {
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
          "Access-Control-Allow-Origin": "*",
        },
      };
      try {
        const res = await axios.get(
          this.$config.BACKEND_URL + "/receipts/filters" + filters,
          payload
        );
        this.receipts = res.data;
        for (const receipt of this.receipts) {
          receipt.created_at = this.convertDateTime(receipt.created_at);
        }
      } catch (error) {
        this.validationErrors = error.response.data;
      }
    },

    onFilter() {
      this.validationErrors = null;
      if (
        (this.startDate && !this.endDate) ||
        (!this.startDate && this.endDate)
      ) {
        this.validationErrors = "Both start and end dates must be set!";
        return;
      }

      if (!(this.startDate || this.endDate || this.item)) {
        this.validationErrors = "Must be date range or name!";
        return;
      }

      this.getFilteredReceipts();
    },

    onClear() {
      this.getReceipts();
    },

    convertDateTime(time) {
      const dateTime = new Date(time);
      return (
        dateTime.toLocaleDateString() + " - " + dateTime.toLocaleTimeString()
      );
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

.filters-section {
  margin-top: 10px;
  display: flex;
  flex-direction: row;
}

.filter-label {
  margin-left: 10px;
  margin-right: 5px;
}

.error-section {
  color: red;
  margin-top: 20px;
  font-size: 12px;
}

.filter-button {
  margin-left: 10px;
  padding: 2px;
  border-radius: 7px;
  cursor: pointer;
}

.receipt {
  padding: 5px;
}

.item {
  padding-top: 2px;
  padding-bottom: 2px;
}
</style>