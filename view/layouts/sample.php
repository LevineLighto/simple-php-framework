<!DOCTYPE html>
<html lang="en">
    <?php view('partials.sample.head'); ?>
<body>
    <div class="min-vh-100 py-5">
        <div class="container py-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <?php view('partials.sample.header'); ?>
                    <main>
                        <?php view($view, $viewData); ?>
                    </main>
                    <?php view('partials.sample.footer'); ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>