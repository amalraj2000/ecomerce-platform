<script setup>
</script>

<template>
  <footer class="bg-white dark:bg-slate-950 border-t border-slate-200 dark:border-slate-800 py-12 mt-12 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <div>
          <h3 class="text-sm font-semibold text-slate-900 dark:text-white uppercase tracking-wider">About KartFlip</h3>
          <ul class="mt-4 space-y-4">
            <li><a href="#" class="text-base text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400">Contact Us</a></li>
            <li><a href="#" class="text-base text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400">About Us</a></li>
            <li><a href="#" class="text-base text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400">Careers</a></li>
          </ul>
        </div>
        <div>
          <h3 class="text-sm font-semibold text-slate-900 dark:text-white uppercase tracking-wider">Help</h3>
          <ul class="mt-4 space-y-4">
            <li><a href="#" class="text-base text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400">Payments</a></li>
            <li><a href="#" class="text-base text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400">Shipping</a></li>
            <li><a href="#" class="text-base text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400">Cancellation & Returns</a></li>
          </ul>
        </div>
        <div>
          <h3 class="text-sm font-semibold text-slate-900 dark:text-white uppercase tracking-wider">Consumer Policy</h3>
          <ul class="mt-4 space-y-4">
            <li><a href="#" class="text-base text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400">Cancellation & Returns</a></li>
            <li><a href="#" class="text-base text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400">Terms of Use</a></li>
            <li><a href="#" class="text-base text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400">Security</a></li>
          </ul>
        </div>
        <div>
          <h3 class="text-sm font-semibold text-slate-900 dark:text-white uppercase tracking-wider">Subscribe to our newsletter</h3>
          <p class="mt-4 text-base text-slate-600 dark:text-slate-400">
            Get the latest updates on new products and upcoming sales.
          </p>
          <form class="mt-4 sm:flex sm:max-w-md">
            <label for="email-address" class="sr-only">Email address</label>
            <input type="email" name="email-address" id="email-address" autocomplete="email" required class="appearance-none min-w-0 w-full bg-slate-100 dark:bg-slate-900 border border-transparent rounded-md shadow-sm py-2 px-4 text-base text-slate-900 dark:text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white dark:focus:bg-slate-800 transition-colors" placeholder="Enter your email">
            <div class="mt-3 rounded-md sm:mt-0 sm:ml-3 sm:flex-shrink-0">
              <button type="submit" class="w-full bg-blue-600 border border-transparent rounded-md shadow-sm py-2 px-4 flex items-center justify-center text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                Subscribe
              </button>
            </div>
          </form>
        </div>
      </div>
      <div class="mt-12 border-t border-slate-200 dark:border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center">
        <p class="text-base text-slate-500 dark:text-slate-400 xl:text-center">
          &copy; 2026 KartFlip Marketplace. All rights reserved.
        </p>
        <div class="flex space-x-6 mt-4 md:mt-0 text-slate-400">
            <!-- Social icons could go here -->
            <svg class="h-6 w-6 hover:text-blue-500 cursor-pointer transition-colors" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
            </svg>
        </div>
      </div>
    </div>
  </footer>
</template>
