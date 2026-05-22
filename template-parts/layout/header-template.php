<?php
/**
 * Footer Bottom / Header Layout
 *
 * @package Octoways-theme
 */

$cart_count = 0;
if (function_exists('WC') && isset(WC()->cart) && WC()->cart) {
           $cart_count = WC()->cart->get_cart_contents_count();
}
?>
<div class="ht-header-wrapper">
           <!-- Top Header row: Search, Logo, Action Icons -->
           <div class="ht-header-top kl-container items-center flex justify-between py-4">
                      <!-- Search -->
                      <div class="ht-header-search hidden lg:flex items-center">
                                 <form action="<?php echo esc_url(home_url('/')); ?>" method="get"
                                            class="ht-search-form w-full max-w-xs">
                                            <div
                                                       class="ht-search-input-group flex items-center border-b border-gray-300 pb-1">
                                                       <span class="ht-search-icon mr-2">
                                                                  <svg width="18" height="18" viewBox="0 0 24 24"
                                                                             fill="none" stroke="#666"
                                                                             stroke-width="1.8" stroke-linecap="round"
                                                                             stroke-linejoin="round">
                                                                             <circle cx="11" cy="11" r="8" />
                                                                             <line x1="21" y1="21" x2="16.65"
                                                                                        y2="16.65" />
                                                                  </svg>
                                                       </span>
                                                       <input type="text" name="s"
                                                                  class="ht-search-input bg-transparent border-none outline-none text-sm w-full placeholder-gray-500"
                                                                  placeholder="Search"
                                                                  value="<?php echo get_search_query(); ?>">
                                            </div>
                                 </form>
                      </div>

                      <!-- Mobile Toggle (Left on mobile, hidden on desktop) -->
                      <div class="ht-mobile-toggle flex lg:hidden w-1/3">
                                 <button id="ht-menu-toggle" class="ht-menu-btn" aria-label="Toggle Menu">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#333"
                                                       stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                       <line x1="3" y1="12" x2="21" y2="12"></line>
                                                       <line x1="3" y1="6" x2="21" y2="6"></line>
                                                       <line x1="3" y1="18" x2="21" y2="18"></line>
                                            </svg>
                                 </button>
                      </div>

                      <!-- Logo -->
                      <div class="ht-header-logo flex items-center justify-center lg:w-1/3 w-1/3">
                                 <?php
                                 if (function_exists('has_custom_logo') && has_custom_logo()) {
                                            the_custom_logo();
                                 } else {
                                            echo '<a href="' . esc_url(home_url('/')) . '" class="text-2xl font-bold text-gray-800">' . esc_html(get_bloginfo('name')) . '</a>';
                                 }
                                 ?>
                      </div>

                      <!-- Action Icons -->
                      <div class="ht-header-actions flex items-center justify-end gap-4 lg:gap-6 lg:w-1/3 w-1/3">
                                 <!-- User Icon -->
                                 <a href="<?php echo esc_url(home_url('/my-account/')); ?>"
                                            class="ht-action-link hidden sm:flex items-center">
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#333"
                                                       stroke-width="1.5" stroke-linecap="round"
                                                       stroke-linejoin="round">
                                                       <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                                       <circle cx="12" cy="7" r="4" />
                                            </svg>
                                            <span
                                                       class="ml-2 text-sm text-gray-700 hidden lg:inline-block font-medium">Account</span>
                                 </a>
                                 <!-- Heart Icon -->
                                 <a href="<?php echo esc_url(home_url('/wishlist/')); ?>" class="ht-action-link">
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#333"
                                                       stroke-width="1.5" stroke-linecap="round"
                                                       stroke-linejoin="round">
                                                       <path
                                                                  d="M20.8 4.6a5.5 5.5 0 0 0-7.7 0l-1.1 1-1.1-1a5.5 5.5 0 0 0-7.8 7.8l1 1 7.8 7.8 7.8-7.7 1-1.1a5.5 5.5 0 0 0 0-7.8z" />
                                            </svg>
                                 </a>
                                 <!-- Cart Icon -->
                                 <a href="<?php echo function_exists('wc_get_cart_url') ? esc_url(wc_get_cart_url()) : '#'; ?>"
                                            class="ht-action-link ht-cart-link relative">
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#333"
                                                       stroke-width="1.5" stroke-linecap="round"
                                                       stroke-linejoin="round">
                                                       <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" />
                                                       <line x1="3" y1="6" x2="21" y2="6" />
                                                       <path d="M16 10a4 4 0 0 1-8 0" />
                                            </svg>
                                            <span
                                                       class="ht-cart-badge absolute -top-2 -right-2 bg-red-600 text-white text-[10px] font-bold w-5 h-5 flex items-center justify-center rounded-full"><?php echo esc_html($cart_count); ?></span>
                                 </a>
                      </div>
           </div>

           <!-- Navigation Menu Row -->
           <div class="ht-header-nav kl-container hidden lg:block border-t border-border-color-100 mt-2">
                      <nav class="ht-main-nav relative">
                                 <ul class="flex justify-center flex-wrap gap-8 py-4">
                                            <?php
                                            $shop_page_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/');
                                            ?>
                                            <li class="ht-nav-item"><a href="<?php echo esc_url($shop_page_url); ?>"
                                                                  class="text-normal  menu-item-parent">Shop All</a>
                                            </li>
                                            <?php
                                            $custom_taxonomies = [
                                                       'jewellery' => 'Jewellery',
                                                       'by_religious' => 'By Religious',
                                                       //'nepali_heritage' => 'Nepali Heritage',
                                                       'shop_by_bond' => 'Shop by Bond',
                                                       'luxury_within_reach' => 'Luxury within Reach',
                                                       'for_whom' => 'For Whom',
                                            ];

                                            foreach ($custom_taxonomies as $tax_slug => $tax_label):
                                                       $parent_terms = get_terms([
                                                                  'taxonomy' => $tax_slug,
                                                                  'parent' => 0,
                                                                  'hide_empty' => false,
                                                       ]);

                                                       if (!empty($parent_terms) && !is_wp_error($parent_terms)):
                                                                  ?>
                                                                  <li class="ht-nav-item ht-has-submenu group">
                                                                             <a href="#"
                                                                                        class="normal-text menu-item-parent flex items-center gap-1">
                                                                                        <?php echo esc_html($tax_label); ?>
                                                                                        <svg class="ht-dropdown-icon transition-transform duration-300"
                                                                                                   width="12" height="12" viewBox="0 0 24 24"
                                                                                                   fill="none" stroke="currentColor"
                                                                                                   stroke-width="2.5" stroke-linecap="round"
                                                                                                   stroke-linejoin="round">
                                                                                                   <polyline points="6 9 12 15 18 9">
                                                                                                   </polyline>
                                                                                        </svg>
                                                                             </a>
                                                                             <div
                                                                                        class="ht-submenu absolute top-full w-full left-1/2 -translate-x-1/2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                                                                                        <!-- Top border for active menu -->
                                                                                        <div
                                                                                                   class="header-active-meu bg-bg-color-100 border-t-2 border-primary-600 shadow-lg w-full max-w-[95vw] flex rounded-b-lg transform translate-y-3 group-hover:translate-y-0 transition-transform overflow-hidden">

                                                                                                   <!-- Data Columns Container -->
                                                                                                   <div
                                                                                                              class="flex-1 p-10 grid grid-cols-5 gap-8 whitespace-nowrap">
                                                                                                              <?php foreach ($parent_terms as $parent_term): ?>
                                                                                                                         <div
                                                                                                                                    class="flex flex-col gap-10">
                                                                                                                                    <div>
                                                                                                                                               <h4
                                                                                                                                                          class="submenu-top-title">
                                                                                                                                                          <a href="<?php echo esc_url(get_term_link($parent_term)); ?>"
                                                                                                                                                                     class="submenu-top-title__link"><?php echo esc_html($parent_term->name); ?></a>
                                                                                                                                               </h4>
                                                                                                                                               <?php
                                                                                                                                               $child_terms = get_terms([
                                                                                                                                                          'taxonomy' => $tax_slug,
                                                                                                                                                          'parent' => $parent_term->term_id,
                                                                                                                                                          'hide_empty' => false,
                                                                                                                                               ]);
                                                                                                                                               if (!empty($child_terms) && !is_wp_error($child_terms)):
                                                                                                                                                          ?>
                                                                                                                                                          <ul
                                                                                                                                                                     class="ht-child-menu-list">
                                                                                                                                                                     <?php foreach ($child_terms as $child_term): ?>
                                                                                                                                                                                <li><a href="<?php echo esc_url(get_term_link($child_term)); ?>"
                                                                                                                                                                                                      class="ht-child-menu-link abc"><?php echo esc_html($child_term->name); ?></a>
                                                                                                                                                                                </li>
                                                                                                                                                                     <?php endforeach; ?>
                                                                                                                                                          </ul>
                                                                                                                                               <?php endif; ?>
                                                                                                                                    </div>
                                                                                                                         </div>
                                                                                                              <?php endforeach; ?>
                                                                                                   </div>

                                                                                                   <!-- Image Column -->
                                                                                                   <!-- <div
                                                                                                              class="w-[320px] bg-gray-100 border-l border-gray-100 relative group overflow-hidden flex items-end p-8 hidden xl:flex shrink-0">
                                                                                                              <div
                                                                                                                         class="absolute inset-0 bg-gradient-to-b from-gray-200/50 to-gray-800/90 mix-blend-multiply z-10 transition-opacity">
                                                                                                              </div>
                                                                                                              <div
                                                                                                                         class="absolute inset-x-0 top-[35%] flex justify-center z-20">
                                                                                                                         <h3
                                                                                                                                    class="text-red-800/20 font-serif text-3xl tracking-wide font-medium text-center leading-tight">
                                                                                                                                    <?php echo esc_html($tax_label); ?>
                                                                                                                         </h3>
                                                                                                              </div>
                                                                                                              <div
                                                                                                                         class="relative z-20 w-full transform translate-y-3 group-hover:translate-y-0 transition-transform duration-500">
                                                                                                                         <span
                                                                                                                                    class="text-white/90 text-[10px] uppercase font-bold tracking-[0.25em] block mb-3">Explore</span>
                                                                                                                         <h3
                                                                                                                                    class="text-white font-serif text-[26px] leading-[1.1] mb-1">
                                                                                                                                    <?php echo esc_html($tax_label); ?><br>Collection
                                                                                                                                    <span
                                                                                                                                               class="inline-block transition-transform duration-300 group-hover:translate-x-2">&rarr;</span>
                                                                                                                         </h3>
                                                                                                              </div>
                                                                                                   </div> -->
                                                                                        </div>
                                                                             </div>
                                                                  </li>
                                                       <?php else: ?>
                                                                  <li class="ht-nav-item"><a href="#"
                                                                                        class="text-gray-800 font-bold text-xs uppercase tracking-widest hover:text-red-600 transition-colors"><?php echo esc_html($tax_label); ?></a>
                                                                  </li>
                                                       <?php endif; ?>
                                            <?php endforeach; ?>
                                 </ul>
                      </nav>
           </div>

           <!-- Mobile Navigation Menu (Slide-in) -->
           <div id="ht-mobile-nav"
                      class="ht-mobile-nav fixed inset-0 bg-white z-[9999] transform -translate-x-full transition-transform duration-300 lg:hidden w-full max-w-sm border-r shadow-2xl flex flex-col">
                      <!-- Close Header -->
                      <div
                                 class="p-5 flex justify-between items-center border-b border-gray-100 sticky top-0 bg-white z-10 shrink-0">
                                 <div class="h-10 w-fit flex items-center">
                                            <?php
                                            if (function_exists('has_custom_logo') && has_custom_logo()) {
                                                       $custom_logo_id = get_theme_mod('custom_logo');
                                                       $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
                                                       if (has_custom_logo()) {
                                                                  echo '<img src="' . esc_url($logo[0]) . '" alt="' . get_bloginfo('name') . '" class="h-full w-auto object-contain max-h-12">';
                                                       } else {
                                                                  echo '<a href="' . esc_url(home_url('/')) . '" class="text-xl font-bold text-gray-800">' . esc_html(get_bloginfo('name')) . '</a>';
                                                       }
                                            } else {
                                                       echo '<a href="' . esc_url(home_url('/')) . '" class="text-xl font-bold text-gray-800">' . esc_html(get_bloginfo('name')) . '</a>';
                                            }
                                            ?>
                                 </div>
                                 <button id="ht-menu-close"
                                            class="p-2 bg-gray-50 rounded-full hover:bg-gray-100 transition-colors text-gray-600"
                                            aria-label="Close Menu">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                       stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                                                       stroke-linejoin="round">
                                                       <line x1="18" y1="6" x2="6" y2="18"></line>
                                                       <line x1="6" y1="6" x2="18" y2="18"></line>
                                            </svg>
                                 </button>
                      </div>

                      <!-- Menu List -->
                      <div class="flex-grow overflow-y-auto w-full">
                                 <ul class="flex flex-col w-full text-left">
                                            <li class="border-b border-gray-100">
                                                       <a href="<?php echo esc_url($shop_page_url ?? home_url('/shop/')); ?>"
                                                                  class="block w-full px-6 py-[18px] text-red-500 font-semibold text-[17px] hover:bg-gray-50 transition-colors">Shop
                                                                  All</a>
                                            </li>

                                            <?php
                                            if (!isset($custom_taxonomies)) {
                                                       $custom_taxonomies = [
                                                                  'jewellery' => 'Jewellery',
                                                                  'by_religious' => 'By Religious',
                                                                  'nepali_heritage' => 'Nepali Heritage',
                                                                  'shop_by_bond' => 'Shop by Bond',
                                                                  'luxury_within_reach' => 'Luxury within Reach',
                                                                  'for_whom' => 'For Whom',
                                                       ];
                                            }

                                            foreach ($custom_taxonomies as $tax_slug => $tax_label):
                                                       $parent_terms = get_terms([
                                                                  'taxonomy' => $tax_slug,
                                                                  'parent' => 0,
                                                                  'hide_empty' => false,
                                                       ]);

                                                       if (!empty($parent_terms) && !is_wp_error($parent_terms)):
                                                                  ?>
                                                                  <li class="ht-mobile-has-submenu border-b border-gray-100">
                                                                             <div
                                                                                        class="flex w-full justify-between items-center cursor-pointer ht-mobile-submenu-toggle px-6 py-[18px] hover:bg-gray-50 transition-colors">
                                                                                        <span
                                                                                                   class="font-medium text-[17px] text-gray-800 tracking-wide"><?php echo esc_html($tax_label); ?></span>
                                                                                        <svg width="18" height="18" viewBox="0 0 24 24"
                                                                                                   fill="none" stroke="currentColor"
                                                                                                   stroke-width="2" stroke-linecap="round"
                                                                                                   stroke-linejoin="round"
                                                                                                   class="transform transition-transform text-gray-600">
                                                                                                   <polyline points="6 9 12 15 18 9">
                                                                                                   </polyline>
                                                                                        </svg>
                                                                             </div>
                                                                             <div class="ht-mobile-submenu">
                                                                                        <ul class="overflow-hidden bg-white">
                                                                                                   <div class="px-8 pb-6 pt-2">
                                                                                                              <!-- View All items for this taxonomy -->
                                                                                                              <!-- <a href="#"
                                                                                                                         class="flex text-slate-800 font-bold text-[14px] uppercase tracking-wider mb-6 items-center group hover:text-red-600 transition-colors">
                                                                                                                         Explore All
                                                                                                                         <?php echo esc_html($tax_label); ?>
                                                                                                                         <span
                                                                                                                                    class="inline-block ml-2 transition-transform duration-300 group-hover:translate-x-1">&rarr;</span>
                                                                                                              </a> -->
                                                                                                              <div
                                                                                                                         class="border-l-2 border-red-200/60 ml-1 pl-5 space-y-6">
                                                                                                                         <?php foreach ($parent_terms as $parent_term): ?>
                                                                                                                                    <div>
                                                                                                                                               <span
                                                                                                                                                          class="block text-slate-500 font-bold text-[13px] uppercase tracking-wider mb-4"><a
                                                                                                                                                                     href="<?php echo esc_url(get_term_link($parent_term)); ?>"><?php echo esc_html($parent_term->name); ?></a></span>
                                                                                                                                               <?php
                                                                                                                                               $child_terms = get_terms([
                                                                                                                                                          'taxonomy' => $tax_slug,
                                                                                                                                                          'parent' => $parent_term->term_id,
                                                                                                                                                          'hide_empty' => false,
                                                                                                                                               ]);
                                                                                                                                               if (!empty($child_terms) && !is_wp_error($child_terms)):
                                                                                                                                                          ?>
                                                                                                                                                          <ul
                                                                                                                                                                     class="space-y-4">
                                                                                                                                                                     <?php foreach ($child_terms as $child_term): ?>
                                                                                                                                                                                <li><a href="<?php echo esc_url(get_term_link($child_term)); ?>"
                                                                                                                                                                                                      class="block text-slate-600 font-medium text-[15.5px] hover:text-red-500 transition-colors"><?php echo esc_html($child_term->name); ?></a>
                                                                                                                                                                                </li>
                                                                                                                                                                     <?php endforeach; ?>
                                                                                                                                                          </ul>
                                                                                                                                               <?php endif; ?>
                                                                                                                                    </div>
                                                                                                                         <?php endforeach; ?>
                                                                                                              </div>
                                                                                                   </div>
                                                                                        </ul>
                                                                             </div>
                                                                  </li>
                                                       <?php else: ?>
                                                                  <li class="border-b border-gray-100">
                                                                             <a href="#"
                                                                                        class="block w-full px-6 py-[18px] text-gray-800 font-semibold text-[17px] hover:bg-gray-50 transition-colors"><?php echo esc_html($tax_label); ?></a>
                                                                  </li>
                                                       <?php endif; ?>
                                            <?php endforeach; ?>
                                 </ul>
                      </div>

                      <!-- Bottom Action Links -->
                      <div class="border-t border-gray-100 p-6 bg-white shrink-0 shadow-[0_-5px_20px_rgba(0,0,0,0.02)]">
                                 <ul class="space-y-5">
                                            <li>
                                                       <a href="<?php echo esc_url(home_url('/my-account/')); ?>"
                                                                  class="flex items-center text-gray-700 font-medium text-[15px] hover:text-red-500 transition-colors">
                                                                  <svg class="mr-3" width="22" height="22"
                                                                             viewBox="0 0 24 24" fill="none"
                                                                             stroke="currentColor" stroke-width="1.5"
                                                                             stroke-linecap="round"
                                                                             stroke-linejoin="round">
                                                                             <path
                                                                                        d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                                                             <circle cx="12" cy="7" r="4" />
                                                                  </svg>
                                                                  Login / Signup
                                                       </a>
                                            </li>
                                            <li>
                                                       <a href="<?php echo esc_url(home_url('/wishlist/')); ?>"
                                                                  class="flex items-center text-gray-700 font-medium text-[15px] hover:text-red-500 transition-colors">
                                                                  <svg class="mr-3" width="22" height="22"
                                                                             viewBox="0 0 24 24" fill="none"
                                                                             stroke="currentColor" stroke-width="1.5"
                                                                             stroke-linecap="round"
                                                                             stroke-linejoin="round">
                                                                             <path
                                                                                        d="M20.8 4.6a5.5 5.5 0 0 0-7.7 0l-1.1 1-1.1-1a5.5 5.5 0 0 0-7.8 7.8l1 1 7.8 7.8 7.8-7.7 1-1.1a5.5 5.5 0 0 0 0-7.8z" />
                                                                  </svg>
                                                                  My Wishlist
                                                       </a>
                                            </li>

                                 </ul>
                      </div>
           </div>
           <!-- Mobile overlay -->
           <div id="ht-mobile-overlay"
                      class="fixed inset-0 bg-black/50 z-[9998] hidden lg:hidden transition-opacity opacity-0"></div>
</div>