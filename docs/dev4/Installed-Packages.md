# Installed Packages For User Managment Service

### Introduction
 This is the documentation for the packages installed in the course of building the microservice apart from the  preinstalled packages based on the configuration when installing the vue project.
 
 - Vue Router
 - Bootstrap Vue

 
 #### Vue Router
 vue-router is the official router for Vue.js. It deeply integrates with Vue.js core to make building Single Page Applications with Vue.js a breeze. Features include:
 
 - Nested route/view mapping
- Modular, component-based router configuration
 - Route params, query, wildcards
 - View transition effects powered by Vue.js' transition system
-  Fine-grained navigation control
-  Links with automatic active CSS classes
-  HTML5 history mode or hash mode, with auto-fallback in IE9
-  Customizable Scroll Behavior
 ##### Installation
 
###### npm
 ```
 npm install vue-router
 ```
 ##### How to use
  
  Add to the main.js file the lines of code to import and use vue router in the project
  
```
import VueRouter from 'vue-router'

Vue.use(VueRouter)

```
 
 ##### Official Documentation
  See [Documentation Reference](https://router.vuejs.org/).
  
 <hr/>
 
 #### Bootstrap Vue
 
 Bootstrap is a framework to help you design websites faster and easier. It includes HTML and CSS based design templates for typography, forms, buttons, tables, navigation, modals, image carousels, etc. It also gives you support for JavaScript plugins. 
 
 ##### Why BoostrapVue
 
 - Responsive [Mobile First]
 - large Community and Friendly Support
 - Browser Compatibility on cross platform
 - Large Ecosystem
 - Easy to Install and Customize
 
 ##### How to install
 
 ```
 # With npm
 npm i vue bootstrap-vue bootstrap
 
 # With yarn
 yarn add vue bootstrap-vue bootstrap
```
 ##### How to integrate with vue
 
 ```
// app.js
import Vue from 'vue'
import BootstrapVue from 'bootstrap-vue'

Vue.use(BootstrapVue)
```

 ```
 / app.js
 import 'bootstrap/dist/css/bootstrap.css'
 import 'bootstrap-vue/dist/bootstrap-vue.css'
 ```
 
 ##### Official Documentation See [Documentation Reference](https://bootstrap-vue.js.org/).
