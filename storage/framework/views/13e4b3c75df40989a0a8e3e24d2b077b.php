<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['name', 'class' => 'h-5 w-5']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['name', 'class' => 'h-5 w-5']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $icons = [
        'home' => 'fa-solid fa-house',
        'users' => 'fa-solid fa-users',
        'store' => 'fa-solid fa-store',
        'book' => 'fa-solid fa-book',
        'building' => 'fa-solid fa-building',
        'newspaper' => 'fa-regular fa-newspaper',
        'plus' => 'fa-solid fa-plus',
        'tag' => 'fa-solid fa-tag',
        'cog' => 'fa-solid fa-cog',
        'gear' => 'fa-solid fa-gear',
        'calendar-days' => 'fa-solid fa-calendar-days',
        'image' => 'fa-regular fa-image',
        'credit-card' => 'fa-regular fa-credit-card',
        'shopping-cart' => 'fa-solid fa-cart-shopping',
        'circle-info' => 'fa-solid fa-circle-info',
        'handshake' => 'fa-solid fa-handshake-simple',
        'clipboard-list' => 'fa-solid fa-clipboard-list',
    ];

    $iconClass = $icons[(string) ($name ?? '')] ?? 'fa-solid fa-circle';
?>

<span class="inline-flex items-center justify-center <?php echo e($class); ?>" <?php echo e($attributes); ?>>
    <i class="fa-fw <?php echo e($iconClass); ?>"></i>
</span>
<?php /**PATH C:\fwzan\selaju-system\resources\views/components/icon.blade.php ENDPATH**/ ?>