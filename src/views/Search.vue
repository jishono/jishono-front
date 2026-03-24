<template>
  <div>
    <div id="inputfield" style="min-height: 10px"></div>
    <div class="row">
      <div class="col-xs-2 col-lg-3"></div>
      <div class="col-xs-8 col-lg-6 mx-auto" style="max-width: 600px">
        <div class="row mx-md-5">
          <div class="col">
            <div class="input-group">
              <input
                :value="q"
                v-on:input="q = $event.target.value.toLowerCase().trim()"
                ref="search"
                autofocus
                id="search"
                class="input-lg form-control"
                :placeholder="$t('interface.search_placeholder')"
                type="text"
                v-bind:class="{ 'form-control': true, error: !valid }"
              />
              <button
                v-if="this.q != ''"
                class="btn bg-transparent"
                style="margin-left: -40px; z-index: 100"
                @click="handleClearClick()"
                tabindex="1"
              >
                <i class="fa fa-times"></i>
              </button>
            </div>
          </div>
        </div>
        <div class="row mt-2 mx-md-5">
          <div class="col-9">
            <span v-show="!valid" class="text-danger">{{
              $t("interface.warning")
            }}</span>
            <span v-if="suggestions.length > 0">
              {{ $t("interface.suggestion") }} ->
              <span
                v-for="(suggestion, i) in suggestions"
                :key="suggestion.oppslag"
              >
                <span
                  class="pointer underline"
                  @click="searchSuggestion(suggestion.oppslag)"
                  @keyup.enter="searchSuggestion(suggestion.oppslag)"
                  tabindex="0"
                >
                  {{ suggestion.oppslag }}</span
                >
                <span v-if="i != suggestions.length - 1">,&nbsp;</span>
              </span>
            </span>
          </div>
          <div class="col-3 pl-0 text-right">
            <span v-if="q.length > 0" class="text-right">
              {{ results.length }} 件</span
            >
          </div>
        </div>
        <div v-if="loading && q != ''" class="text-center my-3">
          <i class="fa fa-spinner fa-spin fa-2x"></i>
        </div>
        <div
          v-for="(result, index) in slicedResults"
          :key="result.lemma_id"
          class="row my-2"
        >
          <div class="col px-2">
            <ResultBox
              v-bind:word="result"
              @get-example-sentences="
                getExampleSentences(result.lemma_id, index)
              "
              @kanren-click="handleKanrenClick"
              @feedback-click="openFeedbackDialog(result)"
            />
          </div>
        </div>
        <div
          class="text-center"
          v-if="results.length > sliceEnd"
          @click="sliceEnd = sliceEnd + 30"
        >
          <span class="btn btn-outline-primary shadow-none">
            {{ $t("interface.show_more") }}</span
          >
        </div>
        <div v-if="q != '' && !exactMatch && !loading" class="text-center mt-2">
          <span v-if="$i18n.locale == 'no'"
            >Søk på
            <span style="font-weight: bold">'{{ q }}'</span>
            ga ingen treff.</span
          >
          <span v-if="$i18n.locale == 'ja'">
            <span style="font-weight: bold">'{{ q }}'</span>
            に一致する検索結果がありませんでした。</span
          >
        </div>
        <div v-if="showRequestButton" class="text-center mt-3">
          <button
            class="btn btn-outline-primary shadow-none"
            @click="sendRequest"
            :disabled="requestSent"
          >
            <span v-if="requestSent">
              {{ $t("interface.request_sent") }}
            </span>
            <span v-else>
              {{ $t("interface.request_button") }}
            </span>
          </button>
        </div>
      </div>
      <div class="col-xs-2 col-lg-3"></div>
    </div>
    <About v-if="showAbout" />
    <FeedbackDialog
      @close-feedback-dialog="closeFeedbackDialog"
      v-bind:word="currentWord"
      v-bind:show="showFeedbackDialog"
    />
  </div>
</template>

<script>
import { defineComponent, nextTick } from "vue";

import api from "../api.js";
import About from "../components/About.vue";
import FeedbackDialog from "../components/FeedbackDialog.vue";
import ResultBox from "../components/ResultBox.vue";

export default defineComponent({
  name: "Search",

  components: {
    ResultBox,
    FeedbackDialog,
    About,
  },

  data() {
    return {
      suggestions: [],
      results: [],
      q: "",
      valid: true,
      showFeedbackDialog: false,
      showAbout: false,
      currentWord: null,
      showRequestButton: false,
      requestSent: false,
      sliceEnd: 30,
      searchTimeout: null,
      loading: false,
    };
  },

  methods: {
    getSuggestionList() {
      api.get("/suggestions", { params: { q: this.q } }).then((response) => {
        this.suggestions = response.data;
      });
    },
    searchItems() {
      if (!this.q) {
        this.results = [];
        return;
      }
      this.loading = true;
      const query = this.q;
      api
        .get("/search", { params: { q: query } })
        .then((response) => {
          if (this.q !== query) return;
          this.results = response.data;
          this.showRequestButton =
            this.q !== "" &&
            this.valid &&
            !this.results.some((r) => r.oppslag === this.q);
        })
        .finally(() => {
          if (this.q === query) {
            this.loading = false;
          }
        });
    },
    debouncedSearch() {
      clearTimeout(this.searchTimeout);
      this.results = [];
      this.loading = true;
      this.searchTimeout = setTimeout(() => {
        this.searchItems();
      }, 500);
    },
    validate() {
      if (this.q.length === 0) {
        return true;
      }
      const matches = this.q.match(/^[0-9A-ZÆÅØa-zæåø\s.\-ÉéÒòÔôÀàÎîÊê]+$/);
      return matches ? true : false;
    },
    getExampleSentences(lemma_id, index) {
      if (!this.results[index].example_sentences) {
        api.get("/example_sentences/" + lemma_id).then((response) => {
          this.results[index].example_sentences = response.data;
        });
      }
    },
    searchSuggestion(suggestion) {
      this.q = suggestion;
      nextTick(() => {
        this.$refs.search.focus({
          preventScroll: true,
        });
      });
    },
    handleKanrenClick(query) {
      this.q = query;
    },
    handleClearClick() {
      this.$router.push("/search/");
    },
    openFeedbackDialog(word) {
      this.showFeedbackDialog = true;
      this.currentWord = word;
    },
    closeFeedbackDialog() {
      this.showFeedbackDialog = false;
      this.currentWord = null;
    },
    sendRequest() {
      if (this.requestSent === false) {
        api.post("/request-translation", { request: this.q }).finally(() => {
          this.requestSent = true;
        });
      }
    },
  },

  computed: {
    slicedResults() {
      return this.results.slice(0, this.sliceEnd);
    },
    exactMatch() {
      if (this.results.length === 0) {
        return false;
      }
      return (
        this.valid && this.results[0].oppslag.toLowerCase() === this.q
      );
    },
  },

  created() {
    api.post("/visit");
    this.q = this.$route.params.query || "";
    if (this.q) {
      this.searchItems();
    }
    if (this.$route.path === "/about") {
      this.showAbout = true;
    }
  },

  watch: {
    q: function (val) {
      this.requestSent = false;
      this.sliceEnd = 30;
      this.suggestions = [];
      this.valid = this.validate();
      if (this.valid && this.q.length > 0) {
        this.$router.push("/search/" + val, () => {});
        this.debouncedSearch();
        this.getSuggestionList();
      } else if (this.q === "") {
        clearTimeout(this.searchTimeout);
        this.results = [];
        this.loading = false;
        this.$router.push("/search/", () => {});
      }
      var scrollElement;
      if (val != "" && window.screen.width < 600) {
        scrollElement = document.getElementById("inputfield");
        scrollElement.scrollIntoView();
      } else {
        window.scrollTo(0, 0);
      }
      this.showRequestButton = false;
    },
    $route(to) {
      this.showAbout = false;
      if (to.path === "/search/") {
        this.q = "";
      } else if (to.path === "/about") {
        this.q = "";
        this.showAbout = true;
      } else if (to.params.query && to.params.query !== this.q) {
        this.q = to.params.query;
      }
    },
  },
});
</script>
