import { createRouter, createWebHistory } from "vue-router";
import HomePage from "../views/Admin/HomePage.vue";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/admin",
      name: "admin",
      component: () => import("../views/Admin/HomePage.vue"),
      meta: { requiredAuth: true, requiredAdmin: true },
      children: [
        {
          path: "",
          name: "dashboard",
          component: () => import("../views/Admin/Dashboard.vue"),
        },
        {
          //http://localhosst:5173/admin/account
          path: "account",
          name: "account",
          component: () => import("../views/Admin/Account.vue"),
        },
        {
          //http://localhosst:5173/admin/category
          path: "category",
          name: "category",
          component: () => import("../views/Admin/Category/Category.vue"),
        },
        {
          //http://localhosst:5173/admin/add-category
          path: "add-category",
          name: "addCategory",
          component: () => import("../views/Admin/Category/AddCategory.vue"),
        },
        {
          //http://localhosst:5173/admin/update-category
          path: "update-category/:idCategory",
          name: "updateCategory",
          component: () => import("../views/Admin/Category/UpdateCategory.vue"),
        },
        {
          //http://localhosst:5173/admin/product
          path: "product",
          name: "product",
          component: () => import("../views/Admin/Product/Product.vue"),
        },

        {
          //http://localhosst:5173/admin/add-product
          path: "add-product",
          name: "addProduct",
          component: () => import("../views/Admin/Product/AddProduct.vue"),
        },

        {
          //http://localhosst:5173/admin/add-product
          path: "update-product/:id",
          name: "updateProduct",
          component: () => import("../views/Admin/Product/UpdateProduct.vue"),
        },

        {
          //http://localhosst:5173/admin/order
          path: "order",
          name: "order",
          component: () => import("../views/Admin/Order.vue"),
        },
        {
          //http://localhosst:5173/admin/order
          path: "/orderdetail/:order_id",
          name: "ordeDetailr",
          component: () => import("../views/Admin/OrderDetail.vue"),
        },
      ],
    },
    {
      path: "/",
      name: "user",
      component: () => import("../views/User/HomePage.vue"),
      children: [
        {
          path: "",
          name: "trang-chu",
          component: () => import("../views/User/Dashboard.vue"),
        },
        {
          path: "san-pham/:idCategory",
          name: "sanpham",
          component: () => import("../views/User/Product.vue"),
        },
        {
          path: "san-pham-chi-tiet/:id",
          name: "sanphamchitiet",
          component: () => import("../views/User/ProductDetail.vue"),
        },
        {
          path: "/giohang",
          name: "giohang",
          component: () => import("../views/User/Cart.vue"),
        },
        {
          path: "/thanhtoan",
          name: "thanhtoan",
          component: () => import("../views/User/CheckOut.vue"),
        },
        {
          path: "/donhang",
          name: "donhang",
          component: () => import("../views/User/Order.vue"),
        },
        {
          path: "/chitietdonhang/:order_id",
          name: "chitietdonhang",
          component: () => import("../views/User/OrderDetail.vue"),
        },
      ],
    },
    {
      path: '/login',
      name: 'login',
      component: () => import("../views/Login.vue"),
    },
    {
      path: '/dang-ky',
      name: 'dangky',
      component: () => import("../views/DangKy.vue"),
    }
  ],
});

router.beforeEach((to, form, next) => {
  let userLogin = localStorage.getItem("userLogin")
  if (to.meta.requiredAuth && userLogin == null) {
    next({ path: '/login' })
  } else {
    userLogin = JSON.parse(userLogin)
    if (to.meta.requiredAdmin && userLogin.role != 'admin') {
      next({ path: '/' })
    } else {
      next()
    }
  }
})
export default router;
