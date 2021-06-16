<template>
  <div>
    <jet-banner />

    <div class="min-h-screen bg-gray-100">
      <nav class="bg-white border-b border-gray-100">
        <!-- Primary Navigation Menu -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between h-16">
            <div class="flex">
              <!-- Logo -->
              <div class="flex-shrink-0 flex items-center">
                <inertia-link :href="route('dashboard')">
                  <jet-application-mark class="block h-9 w-auto" />
                </inertia-link>
              </div>

              <!-- Navigation Links -->
              <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                <jet-nav-link
                  v-show="roleArray.some((role) => role === 'manager')"
                  :href="route('dashboard')"
                  :active="route().current('dashboard')"
                >
                  Thong ke
                </jet-nav-link>
                <jet-nav-link
                  v-show="roleArray.some((role) => role === 'manager')"
                  :href="route('employees.index')"
                  :active="route().current('employees.index')"
                >
                  Nhan vien
                </jet-nav-link>
                <jet-nav-link
                  v-show="roleArray.some((role) => role === 'manager')"
                  :href="route('inventories.index')"
                  :active="route().current('inventories.index')"
                >
                  Hang ton kho
                </jet-nav-link>
                <jet-nav-link
                  v-if="roleArray.some((role) => role === 'manager')"
                  :href="route('employee.timesheet.index')"
                  :active="route().current('employee.timesheet.index')"
                >
                  Lap phieu luong
                </jet-nav-link>
                <jet-nav-link
                  v-if="roleArray.some((role) => role === `cook`)"
                  :href="route('chef.orders.index')"
                  :active="route().current('chef.orders.index')"
                >
                  Don hang
                </jet-nav-link>

                <jet-nav-link
                  v-show="roleArray.some((role) => role === 'manager')"
                  preserveState
                  :href="route('menus.index')"
                  :active="route().current('menus.index')"
                >
                  Thuc don
                </jet-nav-link>
                <jet-nav-link
                  v-show="
                    roleArray.some(
                      (role) =>
                        role === 'host' ||
                        role === 'waiter' ||
                        role === 'busboy'
                    )
                  "
                  :href="route('tables.index')"
                  :active="route().current('tables.index')"
                >
                  Ban
                </jet-nav-link>
                <jet-nav-link
                  v-show="roleArray.some((role) => role !== 'manager')"
                  :href="route('employees.show', $page.props.user.id)"
                  :active="
                    route().current('employees.show', $page.props.user.id)
                  "
                >
                  Thoi gian bieu
                </jet-nav-link>
              </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ml-6">
              <!-- Notification -->
              <div class="ml-3 relative">
                <!-- Teams Dropdown -->
                <jet-dropdown align="right" width="72">
                  <template #trigger>
                    <span class="inline-flex rounded-md">
                      <button
                        type="button"
                        class="
                          inline-flex
                          items-center
                          px-3
                          py-2
                          border border-transparent
                          text-sm
                          leading-4
                          font-medium
                          rounded-md
                          text-gray-500
                          bg-white
                          hover:bg-gray-50
                          hover:text-gray-700
                          focus:outline-none
                          focus:bg-gray-50
                          active:bg-gray-50
                          transition
                          ease-in-out
                          duration-150
                        "
                      >
                        <svg
                          class="ml-2 -mr-0.5 h-4 w-4"
                          xmlns="http://www.w3.org/2000/svg"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke="currentColor"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            fill-rule="evenodd"
                            clip-rule="evenodd"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                          />
                        </svg>
                        <div class="absolute right-0 top-1 text-green-400">
                          {{ unreadNotifications }}
                        </div>
                      </button>
                    </span>
                  </template>

                  <template #content>
                    <h1 class="block px-4 py-2">Thong bao</h1>
                    <div id="parentNotificationBox" class="overflow-auto h-96">
                      <div v-if="notifications.length > 0" id="notificationBox">
                        <div
                          v-for="notify in notifications"
                          :key="notify.id"
                          class="group relative inline-flex w-full"
                        >
                          <jet-dropdown-link
                            @click.prevent="markAsRead(notify)"
                            :href="notify.data.link"
                          >
                            <span
                              :class="{
                                'text-gray-400': notify.read_at !== null,
                              }"
                            >
                              {{ notify.data.message }}
                            </span>
                            <div>
                              {{ notify.created_at }}
                            </div>
                          </jet-dropdown-link>

                          <div
                            class="flex self-center absolute right-4"
                            v-if="notify.read_at === null"
                          >
                            <jet-dropdown align="right" width="72">
                              <template #trigger>
                                <span
                                  class="
                                    opacity-0
                                    group-hover:opacity-100
                                    absolute
                                    right-0
                                    inline-flex
                                    rounded-md
                                    border
                                    rounded-full
                                  "
                                >
                                  <button
                                    type="button"
                                    class="
                                      rounded-full
                                      inline-flex
                                      items-center
                                      px-1
                                      py-1
                                      border border-transparent
                                      text-sm
                                      leading-4
                                      font-medium
                                      rounded-md
                                      text-gray-500
                                      bg-white
                                      hover:bg-gray-50
                                      hover:text-gray-700
                                      focus:outline-none
                                      focus:bg-gray-50
                                      active:bg-gray-50
                                      transition
                                      ease-in-out
                                      duration-150
                                    "
                                  >
                                    <svg
                                      xmlns="http://www.w3.org/2000/svg"
                                      class="h-6 w-6"
                                      fill="none"
                                      viewBox="0 0 24 24"
                                      stroke="currentColor"
                                    >
                                      <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"
                                      />
                                    </svg>
                                  </button>
                                </span>
                              </template>

                              <template #content>
                                <div class="relative inline-flex w-full pl-5">
                                  <jet-dropdown-link
                                    width="w-full"
                                    @click.prevent="markAsRead(notify)"
                                    :href="notify.data.link"
                                  >
                                    <div class="inline-block w-1/6">
                                      <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-6 w-6"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                      >
                                        <path
                                          stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M5 13l4 4L19 7"
                                        />
                                      </svg>
                                    </div>
                                    <span class="inline-block w-5/6">
                                      Mark as read
                                    </span>
                                  </jet-dropdown-link>
                                </div>
                                <div class="relative inline-flex w-full pl-5">
                                  <jet-dropdown-link
                                    width="w-full"
                                    @click.prevent="removeNotification(notify)"
                                    :href="route(`tables.index`)"
                                  >
                                    <div class="inline-block w-1/6">
                                      <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-6 w-6"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                      >
                                        <path
                                          stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                        />
                                      </svg>
                                    </div>
                                    <span class="inline-block w-5/6">
                                      Remove this notification.
                                    </span>
                                  </jet-dropdown-link>
                                </div>
                              </template>
                            </jet-dropdown>
                            <div class="rounded-full bg-blue-900 w-3 h-3"></div>
                          </div>
                        </div>
                      </div>
                      <div v-else>You don't have any notifications.</div>
                      <div class="border-t border-gray-100"></div>
                    </div>
                  </template>
                </jet-dropdown>
              </div>

              <!-- Settings Dropdown -->
              <div class="ml-3 relative">
                <jet-dropdown align="right" width="48">
                  <template #trigger>
                    <button
                      v-if="$page.props.jetstream.managesProfilePhotos"
                      class="
                        flex
                        text-sm
                        border-2 border-transparent
                        rounded-full
                        focus:outline-none
                        focus:border-gray-300
                        transition
                        duration-150
                        ease-in-out
                      "
                    >
                      <img
                        class="h-8 w-8 rounded-full object-cover"
                        :src="$page.props.user.profile_photo_url"
                        :alt="$page.props.user.name"
                      />
                    </button>

                    <span v-else class="inline-flex rounded-md">
                      <button
                        type="button"
                        class="
                          inline-flex
                          items-center
                          px-3
                          py-2
                          border border-transparent
                          text-sm
                          leading-4
                          font-medium
                          rounded-md
                          text-gray-500
                          bg-white
                          hover:text-gray-700
                          focus:outline-none
                          transition
                          ease-in-out
                          duration-150
                        "
                      >
                        {{ $page.props.user.name }}

                        <svg
                          class="ml-2 -mr-0.5 h-4 w-4"
                          xmlns="http://www.w3.org/2000/svg"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd"
                          />
                        </svg>
                      </button>
                    </span>
                  </template>

                  <template #content>
                    <!-- Account Management -->
                    <div class="block px-4 py-2 text-xs text-gray-400">
                      Quan ly tai khoan
                    </div>

                    <jet-dropdown-link :href="route('profile.show')">
                      Profile
                    </jet-dropdown-link>

                    <jet-dropdown-link
                      :href="route('api-tokens.index')"
                      v-if="$page.props.jetstream.hasApiFeatures"
                    >
                      API Tokens
                    </jet-dropdown-link>

                    <div class="border-t border-gray-100"></div>

                    <!-- Authentication -->
                    <form @submit.prevent="logout">
                      <jet-dropdown-link as="button">
                        Dang xuat
                      </jet-dropdown-link>
                    </form>
                  </template>
                </jet-dropdown>
              </div>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
              <button
                @click="showingNavigationDropdown = !showingNavigationDropdown"
                class="
                  inline-flex
                  items-center
                  justify-center
                  p-2
                  rounded-md
                  text-gray-400
                  hover:text-gray-500
                  hover:bg-gray-100
                  focus:outline-none
                  focus:bg-gray-100
                  focus:text-gray-500
                  transition
                  duration-150
                  ease-in-out
                "
              >
                <svg
                  class="h-6 w-6"
                  stroke="currentColor"
                  fill="none"
                  viewBox="0 0 24 24"
                >
                  <path
                    :class="{
                      hidden: showingNavigationDropdown,
                      'inline-flex': !showingNavigationDropdown,
                    }"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"
                  />
                  <path
                    :class="{
                      hidden: !showingNavigationDropdown,
                      'inline-flex': showingNavigationDropdown,
                    }"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"
                  />
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Responsive Navigation Menu -->
        <div
          :class="{
            block: showingNavigationDropdown,
            hidden: !showingNavigationDropdown,
          }"
          class="sm:hidden"
        >
          <div class="pt-2 pb-3 space-y-1">
            <jet-responsive-nav-link
              :href="route('dashboard')"
              :active="route().current('dashboard')"
            >
              Thong ke
            </jet-responsive-nav-link>
            <jet-responsive-nav-link
              v-show="roleArray.some((role) => role === 'manager')"
              :href="route('employees.index')"
              :active="route().current('employees.index')"
            >
              Nhan vien
            </jet-responsive-nav-link>
            <jet-responsive-nav-link
              v-show="roleArray.some((role) => role === 'manager')"
              :href="route('inventories.index')"
              :active="route().current('inventories.index')"
            >
              Hang ton kho
            </jet-responsive-nav-link>
            <jet-responsive-nav-link
              v-if="roleArray.some((role) => role === 'manager')"
              :href="route('employee.timesheet.index')"
              :active="route().current('employee.timesheet.index')"
            >
              Lap phieu luong
            </jet-responsive-nav-link>
            <jet-responsive-nav-link
              v-if="roleArray.some((role) => role === `cook`)"
              :href="route('chef.orders.index')"
              :active="route().current('chef.orders.index')"
            >
              Don hang
            </jet-responsive-nav-link>

            <jet-responsive-nav-link
              v-show="roleArray.some((role) => role === 'manager')"
              preserveState
              :href="route('menus.index')"
              :active="route().current('menus.index')"
            >
              Thuc don
            </jet-responsive-nav-link>
            <jet-responsive-nav-link
              v-show="
                roleArray.some(
                  (role) =>
                    role === 'host' || role === 'waiter' || role === 'busboy'
                )
              "
              :href="route('tables.index')"
              :active="route().current('tables.index')"
            >
              Ban
            </jet-responsive-nav-link>
            <jet-responsive-nav-link
              v-show="roleArray.some((role) => role !== 'manager')"
              :href="route('employees.show', $page.props.user.id)"
              :active="route().current('employees.show', $page.props.user.id)"
            >
              Thoi gian bieu
            </jet-responsive-nav-link>
          </div>

          <!-- Responsive Settings Options -->
          <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="flex items-center px-4">
              <div
                v-if="$page.props.jetstream.managesProfilePhotos"
                class="flex-shrink-0 mr-3"
              >
                <img
                  class="h-10 w-10 rounded-full object-cover"
                  :src="$page.props.user.profile_photo_url"
                  :alt="$page.props.user.name"
                />
              </div>

              <div>
                <div class="font-medium text-base text-gray-800">
                  {{ $page.props.user.name }}
                </div>
                <div class="font-medium text-sm text-gray-500">
                  {{ $page.props.user.email }}
                </div>
              </div>
            </div>

            <div class="mt-3 space-y-1">
              <jet-responsive-nav-link
                :href="route('profile.show')"
                :active="route().current('profile.show')"
              >
                Profile
              </jet-responsive-nav-link>

              <jet-responsive-nav-link
                :href="route('api-tokens.index')"
                :active="route().current('api-tokens.index')"
                v-if="$page.props.jetstream.hasApiFeatures"
              >
                API Tokens
              </jet-responsive-nav-link>

              <!-- Authentication -->
              <form method="POST" @submit.prevent="logout">
                <jet-responsive-nav-link as="button">
                  Dang xuat
                </jet-responsive-nav-link>
              </form>
            </div>
          </div>
        </div>
      </nav>

      <!-- Page Heading -->
      <header class="bg-white shadow" v-if="$slots.header">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
          <slot name="header"></slot>
        </div>
      </header>

      <!-- Page Content -->
      <main>
        <slot></slot>
      </main>
    </div>
  </div>
</template>

<script>
import JetApplicationMark from "@/Jetstream/ApplicationMark";
import JetBanner from "@/Jetstream/Banner";
import JetDropdown from "@/Jetstream/Dropdown";
import JetDropdownLink from "@/Jetstream/DropdownLink";
import JetNavLink from "@/Jetstream/NavLink";
import JetResponsiveNavLink from "@/Jetstream/ResponsiveNavLink";
import moment from "moment";
export default {
  components: {
    JetApplicationMark,
    JetBanner,
    JetDropdown,
    JetDropdownLink,
    JetNavLink,
    JetResponsiveNavLink,
  },

  data() {
    return {
      showingNavigationDropdown: false,
      notifications: [],
      total: 0,
      page: 1,
      last_page: 1,
    };
  },

  created() {
    if (Notification.permission === "granted") {
      Echo.private(`App.Models.User.${this.$page.props.user.id}`).notification(
        (notification) => {
          const notificationApi = new Notification(
            "You got a notification from Restaurant!",
            {
              body: notification[0].message,
              icon: "https://banner2.cleanpng.com/20180528/et/kisspng-home-automation-kits-smartthings-building-theme-restaurant-5b0c6800a3c6a8.0624254415275397126708.jpg",
            }
          );
          console.log(notification);
          this.notifications.unshift({
            created_at: moment(
              new Date(notification[0].created),
              "YYYYMMDD"
            ).fromNow(),
            data: notification[0],
            id: notification.id,
            read_at: null,
          });
          notificationApi.onclick = () => {
            window.location.href = notification[0].link;
          };
        }
      );
    } else if (Notification.permission !== "denied") {
      Notification.requestPermission().then((permission) => {
        if (permission === "granted") {
          Echo.private(
            `App.Models.User.${this.$page.props.user.id}`
          ).notification((notification) => {
            this.notifications.unshift({
              created_at: moment(
                new Date(notification[0].created),
                "YYYYMMDD"
              ).fromNow(),
              data: notification[0],
              id: notification.id,
              read_at: null,
            });
          });
        }
      });
    }
  },
  computed: {
    unreadNotifications() {
      return this.notifications.filter((notify) => notify.read_at === null)
        .length;
    },
    notificationsComp() {
      return this.notifications;
    },
    roleArray() {
      return this.$root.roleArrayComputed;
    },
  },
  mounted() {
    this.fetchNotifications();
    let parentNotificationBox = document.querySelector(
      "#parentNotificationBox"
    );

    let self = this;
    parentNotificationBox.addEventListener("scroll", function (event) {
      let height = event.target.scrollTop + event.target.offsetHeight;
      let childrenHeight = event.target.children[0].offsetHeight;
      if (height == childrenHeight) {
        if (self.notifications.length !== self.total) {
          self.page++;
          self.fetchNotifications();
        } else {
          self.page = self.last_page;
        }
      }
    });
  },
  methods: {
    timeSince(date) {
      var seconds = Math.floor((new Date() - date) / 1000);

      var interval = seconds / 31536000;

      if (interval > 1) {
        return Math.floor(interval) + " years";
      }
      interval = seconds / 2592000;
      if (interval > 1) {
        return Math.floor(interval) + " months";
      }
      interval = seconds / 86400;
      if (interval > 1) {
        return Math.floor(interval) + " days";
      }
      interval = seconds / 3600;
      if (interval > 1) {
        return Math.floor(interval) + " hours";
      }
      interval = seconds / 60;
      if (interval > 1) {
        return Math.floor(interval) + " minutes";
      }
      return Math.floor(seconds) + " seconds";
    },
    switchToTeam(team) {
      this.$inertia.put(
        route("current-team.update"),
        {
          team_id: team.id,
        },
        {
          preserveState: false,
        }
      );
    },

    logout() {
      this.$inertia.post(route("logout"), {
        onSuccess: () => (this.$root.roleArray = []),
      });
    },
    markAsRead(notify) {
      fetch(`/notifications/${notify.id}`)
        .then((response) => response.json())
        .then((data) => (this.notifications = data));
    },
    async fetchNotifications() {
      let { data } = await this.$root.$data.axios.get(
        `${window.location.origin}/notifications?page=${this.page}`
      );
      let notifications = data.data.filter(
        (notify) =>
          (notify.created_at = moment(
            new Date(notify.created_at),
            "YYYYMMDD"
          ).fromNow())
      );
      // let notifications = data.data;
      this.total = data.total;
      this.last_page = data.last_page;
      this.notifications = this.notifications.concat(notifications);
    },
    removeNotification(notify) {
      fetch(`/notifications/${notify.id}/delete`)
        .then((response) => response.json())
        .then((data) => (this.notifications = data));
    },
  },
};
</script>
