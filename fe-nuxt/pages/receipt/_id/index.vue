<template>
  <div>
    <NavBar />

    <div class="frame">
      <span class="error-section" v-if="validationErrors">
        {{ this.validationErrors }}
      </span>
      <h2>Receipt: {{ receipt.created_at }}</h2>
      <form
        class="add-receipt-section"
        ref="formName"
        @submit.prevent="onChangeReceipt"
      >
        <input class="change-button" type="submit" value="Change" />

        <div class="input-section">
          <ul>
            <li v-for="item in receipt.items">
              <input
                class="checkbox"
                :id="item.id"
                :value="item.id"
                type="checkbox"
                v-model="checkedItems"
              />
              <label :for="item.id">{{ item.name }}</label>
            </li>

            <li v-for="item in items">
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

      <button class="delete-button" v-on:click="onDeleteReceipt">Delete</button>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "EditReceipt",

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
    this.getReceipt();
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

    async getReceipt() {
      const payload = {
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
          "Access-Control-Allow-Origin": "*",
        },
      };
      try {
        const res = await axios.get(
          this.$config.BACKEND_URL + "/receipts/" + this.$route.params.id,
          payload
        );
        this.receipt = res.data;
        this.receipt.created_at = this.convertDateTime(this.receipt.created_at);
        this.receipt.items.forEach((element) => {
          this.checkedItems.push(element.id);
        });
      } catch (error) {
        this.validationErrors = error.response.data;
      }
    },

    async updateReceipt() {
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
        const res = await axios.put(
          this.$config.BACKEND_URL + "/receipts/" + this.$route.params.id,
          payload
        );
        this.receipt = res.data;
        this.getItems();
        alert("Successfully changed!");
      } catch (error) {
        this.validationErrors = error.response.data;
      }
    },

    async deleteReceipt() {
      const payload = {
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
          "Access-Control-Allow-Origin": "*",
        },
      };

      try {
        const res = await axios.delete(
          this.$config.BACKEND_URL + "/receipts/" + this.$route.params.id,
          payload
        );
        alert("Successfully deleted!");
        this.$router.push("/");
      } catch (error) {
        this.validationErrors = error.response.data;
      }
    },

    onChangeReceipt() {
      if (!this.checkedItems.length) {
        this.validationErrors = "Choose at least one item!";
        return;
      }

      this.updateReceipt();
    },

    onDeleteReceipt() {
      this.deleteReceipt();
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

.add-receipt-section {
  margin-top: 25px;
}

.change-button {
  padding: 5px;
  border-radius: 10px;
  cursor: pointer;
}

.delete-button {
  margin-top: 10px;
  padding: 10px;
  background-color: red;
  width: 80px;
  cursor: pointer;
}

.error-section {
  color: red;
  margin-top: 20px;
  font-size: 12px;
}
</style>