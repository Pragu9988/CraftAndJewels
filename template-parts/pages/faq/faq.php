<?php
/**
 * FAQ Section — Heritage Craft & Jewels
 * Accordion-style FAQ with numbered items, premium branding, and accessible markup.
 */

$faq_items = [
           [
                      'number' => '01',
                      'question' => 'What makes Heritage Craft & Jewels different from other jewellery brands?',
                      'answer' => 'Heritage Craft & Jewels is a Nepal-rooted brand where every piece is handcrafted by our own in-house artisans, drawing inspiration from the rich traditions and cultures of Nepal. We go further by partnering with Atha Pvt. Ltd. — a renowned Indian manufacturer with over 30 years of industry experience — ensuring our jewellery meets international design and finishing standards. The result is a unique blend of Himalayan heritage and contemporary luxury that you won\'t find elsewhere.',
           ],
           [
                      'number' => '02',
                      'question' => 'What types of jewellery and metals do you offer?',
                      'answer' => 'Our collections cover three premium metal categories. Our signature line features 925 Sterling Silver jewellery crafted with precision finishing. We also offer Certified Diamond jewellery — including lab-grown diamond options — and elegant Gold & Rose Gold pieces for every special occasion. Additionally, we provide customised design options so you can create a piece that is entirely your own.',
           ],
           [
                      'number' => '03',
                      'question' => 'How do you ensure the quality of every piece?',
                      'answer' => 'Quality is our core promise. As an ISO 9001:2015 Certified Company, we follow rigorous quality standards at every step. Each piece is thoughtfully handcrafted by our skilled in-house artisans with meticulous attention to detail — from the initial design to the final polish — ensuring perfect finishing, lasting shine, and timeless beauty. Our manufacturing collaboration with Atha Pvt. Ltd. adds a further layer of international-grade quality assurance.',
           ],
           [
                      'number' => '04',
                      'question' => 'Do you offer B2B partnerships for jewellery shops and businesses?',
                      'answer' => 'Yes — B2B partnerships are at the heart of what we do. We are dedicated to fostering long-term business-to-business (B2B) relationships with jewellery shops and retailers. Our offering includes exceptional design variety, competitive pricing, prompt service, and long-term jewellery care support. We take a jewellery-shop-centric approach, meaning your success as a business partner drives every decision we make.',
           ],
           [
                      'number' => '05',
                      'question' => 'Can I order a custom-designed piece, and what is your brand\'s vision?',
                      'answer' => 'Absolutely. We offer a dedicated Custom Order service where our artisans work closely with you to bring your vision to life — from personalised name pendants to engraved couples sets and bespoke bridal pieces. Our brand vision is guided by four pillars: quality craftsmanship, a lifelong commitment with prompt service, elegance & uniqueness in every design, and a jewellery-shop-centric approach that ensures every client — individual or business — feels truly valued.',
           ],
];
?>

<section class="ht-faq" aria-labelledby="faq-section-title">
           <div class="kl-container">

                      <div class="row justify-center mb-10">
                                 <div class="col-xs-12 col-md-10 col-lg-10 text-center">
                                            <div class="strapline mb-2">
                                                       <span>Heritage Craft & Jewels — FAQ</span>
                                            </div>

                                            <h2 class="section-title leading-tight mb-3" id="about-title">
                                                       Frequently Asked Questions
                                            </h2>

                                            <p class="normal-text">
                                                       Everything you need to know about our craftsmanship, collections
                                                       &amp; partnerships.

                                            </p>
                                 </div>
                      </div>

                      <!-- Accordion List -->
                      <div class="ht-faq__list" role="list">
                                 <?php foreach ($faq_items as $index => $item):
                                            $item_id = 'faq-item-' . $item['number'];
                                            $panel_id = 'faq-panel-' . $item['number'];
                                            ?>
                                            <div class="ht-faq__item" role="listitem" id="<?php echo esc_attr($item_id); ?>">
                                                       <button class="ht-faq__question" aria-expanded="false"
                                                                  aria-controls="<?php echo esc_attr($panel_id); ?>"
                                                                  id="<?php echo esc_attr($item_id . '-btn'); ?>" type="button">
                                                                  <span class="ht-faq__number"
                                                                             aria-hidden="true"><?php echo esc_html($item['number']); ?></span>
                                                                  <span
                                                                             class="ht-faq__question-text normal-text"><?php echo esc_html($item['question']); ?></span>
                                                                  <span class="ht-faq__icon" aria-hidden="true">
                                                                             <svg class="ht-faq__icon-plus"
                                                                                        xmlns="http://www.w3.org/2000/svg"
                                                                                        width="18" height="18" viewBox="0 0 24 24"
                                                                                        fill="none" stroke="currentColor"
                                                                                        stroke-width="1.5" stroke-linecap="round"
                                                                                        stroke-linejoin="round">
                                                                                        <line x1="12" y1="5" x2="12" y2="19">
                                                                                        </line>
                                                                                        <line x1="5" y1="12" x2="19" y2="12">
                                                                                        </line>
                                                                             </svg>
                                                                             <svg class="ht-faq__icon-minus"
                                                                                        xmlns="http://www.w3.org/2000/svg"
                                                                                        width="18" height="18" viewBox="0 0 24 24"
                                                                                        fill="none" stroke="currentColor"
                                                                                        stroke-width="1.5" stroke-linecap="round"
                                                                                        stroke-linejoin="round">
                                                                                        <line x1="5" y1="12" x2="19" y2="12">
                                                                                        </line>
                                                                             </svg>
                                                                  </span>
                                                       </button>

                                                       <div class="ht-faq__answer" id="<?php echo esc_attr($panel_id); ?>"
                                                                  role="region"
                                                                  aria-labelledby="<?php echo esc_attr($item_id . '-btn'); ?>"
                                                                  hidden>
                                                                  <div class="ht-faq__answer-inner">
                                                                             <p class="normal-text">
                                                                                        <?php echo esc_html($item['answer']); ?>
                                                                             </p>
                                                                  </div>
                                                       </div>
                                            </div>
                                 <?php endforeach; ?>
                      </div>

           </div>
</section>