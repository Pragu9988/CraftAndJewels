<?php
/**
 * Social profile icon list.
 *
 * @package octoways
 *
 * @var array<int, array{key: string, url: string, label: string, icon: string}> $links
 * @var string $modifier       Optional BEM modifier for the list (e.g. ht-social-list--contact).
 * @var string $wrapper_class  Optional class on the <ul>.
 */

defined('ABSPATH') || exit;

$links = isset($links) && is_array($links) ? $links : array();

if ($links === array()) {
	return;
}

$modifier      = isset($modifier) ? (string) $modifier : '';
$wrapper_class = isset($wrapper_class) ? (string) $wrapper_class : '';

$list_classes = array('ht-social-list');

if ($modifier !== '') {
	$list_classes[] = $modifier;
}

if ($wrapper_class !== '') {
	$list_classes[] = $wrapper_class;
}
?>
<ul class="<?php echo esc_attr(implode(' ', $list_classes)); ?>">
	<?php foreach ($links as $link) : ?>
		<li>
			<a href="<?php echo esc_url($link['url']); ?>"
				target="_blank"
				rel="noopener noreferrer"
				aria-label="<?php echo esc_attr($link['label']); ?>">
				<i class="<?php echo esc_attr($link['icon']); ?>" aria-hidden="true"></i>
			</a>
		</li>
	<?php endforeach; ?>
</ul>
