<style lang="less" scoped>
textarea {
  font-family: inherit;
  font-size: inherit;
  box-sizing: border-box;
  padding: 0px;
  margin: 0;
  outline: none;
  width: 300px;
  overflow: auto;
  line-height: inherit;
  padding: 5px;
  resize: none;
}
</style>

<template>
<div class="einput">
    <textarea :style="{'height':height +'px'}" :rows="rows||2" @input="onChange"  v-model="tempValue"></textarea>
</div>
  
</template>
<script>
export default {
  name: "einput",
  props: {
    autoHeigth: {
      type: Boolean,
      default: false
    },
    value: String,
    rows: String
  },
  data() {
    return {
      height: "",
      minHeight: 0,
      tempValue: "",
      hiddenDiv: null,
      style:
        "visibility: hidden;z-index:-1000;word-wrap: break-word;border: solid 1px #f00;position:fixed;top:0;right:0;padding:5px;max-height:200px;"
    };
  },
  methods: {
    onChange(event) {
      //visibility: hidden;z-index:-1000;
      let _textarea = event.target;
      let _value = _textarea.value;
      this.$emit("input", _value);
      this.setHeight(_value);
    },
    setMinHeight() {
      let _textarea = this.$el.children[0];
      this.minHeight = _textarea.offsetHeight;
    },
    setHeight(value) {
      if (this.autoHeigth) {
        this.hiddenDiv.innerHTML = value.replace(/\n/g, "<br/>|");
        //   console.log(this.hiddenDiv.offsetHeight, this.minHeight)
        if (this.hiddenDiv.offsetHeight >= this.minHeight) {
          this.height = this.hiddenDiv.offsetHeight;
        }
      }
    },
    createdHiddenDiv() {
      if (this.autoHeigth) {
        this.hiddenDiv = document.createElement("DIV");
        this.hiddenDiv.setAttribute("style", this.style);
        this.ayncStyle();
        document.body.appendChild(this.hiddenDiv);
      }
    },
    getTextareaStyle() {
      let _textarea = this.$el.children[0];
      let styleOBJ = window.getComputedStyle(_textarea);
      let width = styleOBJ.getPropertyValue("width");
      let boxSizing = styleOBJ.getPropertyValue("box-sizing");
      return {
        width,
        boxSizing
      };
    },
    ayncStyle() {
      let _style = this.getTextareaStyle();
      this.hiddenDiv.style.width = _style.width;
      this.hiddenDiv.style.boxSizing = _style.boxSizing;
    }
  },
  created() {
    this.tempValue = this.value;
  },
  mounted() {
    this.setMinHeight();
    this.createdHiddenDiv();
    this.setHeight(this.value);
  }
};
</script>
