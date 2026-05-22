<?php
$capabilities = [
  "Logo Design" => "A diverse team of crafty designers produce custom logo designs in line with your brand identity and company goals, with smart use of relevant color schemes, design elements, and shapes.",
  "App Design" => "Our mobile app development team transforms your ideas into easy-to-use, profitable apps, with appealing designs and glitch-free interfaces.",
  "Website Development" => "Smart and responsive web development services by professional tech experts who focus all technical, functional, and visual aspects of your web pages.",
  "AI Solutions" => "We develop intelligent AI-driven systems that automate workflows, enhance decision-making, and improve customer experiences through machine learning and data-driven insights.",
  "Web App Development" => "Our team builds robust, scalable, and high-performing web applications tailored to your business needs using modern technologies and frameworks.",
];


// Remove duplicate values if any
$capabilities = array_unique($capabilities);
?>

<section class="about-us about-introduction pb-7 md:pb-9 lg-pb-12">
  <div class="image-overlay">
    <img src="<?php echo OCTOWAYS_SRC_IMG_URI . '/about/bg.png'; ?>" alt="" srcset="">
  </div>
  <div class="about-introduction__heading mb-3 md:mb-4 lg:mb-7">
    <small class="strapline text-center">About Us</small>
    <h2 class="text-text-400 text-section-title font-medium mb-6 leading-tight text-center">
      Our Commitment to Collaboration, Innovation, and Shaping a Better Future
    </h2>
  </div>
  <div class="kl-container grid grid-cols-1 lg:grid-cols-2 gap-12 about-us__content">
    <!-- Left Content -->
    <div>
     <p class="text-text-400 text-normal-text leading-relaxed mb-4">
  At Octoways, we believe innovation is born when human creativity meets intelligent adaptability. We combine cutting-edge technology with skilled talent to deliver digital solutions that are thoughtful, scalable, and built to drive real business results. Every project is approached with a personal commitment to quality, strategic insight, and a deep understanding of our clients' unique needs.
</p>

<p class="text-text-400 text-normal-text leading-relaxed mb-4">
  Our team thrives in a culture of continuous learning, agile methodologies, and strong collaboration, ensuring we stay ahead in a constantly evolving digital landscape. We focus on user-centric design and real-world impact, working closely with our clients to transform ideas into powerful, lasting solutions. At Octoways, we are not just service providers — we are long-term partners dedicated to shaping smarter, more connected futures through creativity, innovation, and excellence.
</p>

<p class="text-text-400 text-normal-text leading-relaxed">
  Together, we’re redefining what’s possible in the digital world — helping brands grow, adapt, and lead with confidence. From initial discovery to post-launch optimization, our process is driven by data, empathy, and innovation at every stage. We don’t just build digital products; we craft experiences that inspire trust, foster engagement, and deliver measurable value. Whether it’s developing dynamic websites, intuitive mobile apps, or comprehensive digital ecosystems, our mission remains the same — to empower businesses to thrive in an ever-changing world. 
</p>


    </div>
    <!-- Right Content -->
    <div class="relative pl-4">
      <div class="flex flex-col relative space-y-10">
        <!-- Vertical Line -->
        <div class="absolute top-10 left-[0.4em] bottom-0 w-0.5 bg-gray-200"></div>

        <?php foreach ($capabilities as $title => $desc): ?>
          <div class="relative flex items-start">
            <div class="w-4 h-4 bg-white border-2 border-primary-400 rounded-full flex-shrink-0 relative z-10"></div>
            <div class=" ml-6">
              <h3 class="text-subtitle text-text-400 font-medium mb-3 leading-5"><?= htmlspecialchars($title) ?></h3>
              <p class="text-small-text text-text-400 leading-normal">
                <?= htmlspecialchars($desc) ?>
              </p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>