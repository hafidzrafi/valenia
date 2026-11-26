<div class="flex justify-center items-center py-8 relative z-10">
    <div class="flex flex-wrap justify-center gap-8">

        <form action="/form" method="post">
            <input type="hidden" name="heroform" value="POLI UMUM">
            <?php echo csrf_field(); ?>
            <button type="submit" class="w-full text-left">
                <div class="flex flex-col w-80 h-110 bg-[linear-gradient(180deg,rgba(102,102,102,0)_50.48%,rgba(0,0,0,0.7)_78.85%),url(/images/poliumum.webp)] bg-cover bg-center p-4 transition-all transform hover:scale-110 hover:outline hover:outline-4 hover:outline-teal-500 hover:shadow-2xl hover:shadow-teal-700/75 shadow-md shadow-gray-500 active:scale-100">
                    <div class="mt-auto">
                        <h3 class="text-4xl font-bold m-2 text-white">Poli Umum</h3>
                        <div class="scale-80 left-0 -translate-x-4">
                            <?php
                            $status_tag = 'positive';
            $label_tag = 'Buka';

            include __DIR__.'/../components/tag.php'; ?>
                        </div>
                    </div>
                </div>
            </button>
        </form>

        <form action="/form" method="post">
            <input type="hidden" name="heroform" value="POLI GIGI">
            <?php echo csrf_field(); ?>
            <button type="submit" class="w-full text-left">
                <div class="flex flex-col w-80 h-110 bg-[linear-gradient(180deg,rgba(102,102,102,0)_50.48%,rgba(0,0,0,0.7)_78.85%),url(/images/placeholder.jpg)] bg-cover bg-center p-4 transition-all transform hover:scale-110 hover:outline hover:outline-4 hover:outline-teal-500 hover:shadow-2xl hover:shadow-teal-700/75 shadow-md shadow-gray-500 active:scale-100">
                    <div class="mt-auto">
                        <h3 class="text-4xl font-bold m-2 text-white">Poli Gigi</h3>
                        <div class="scale-80 left-0 -translate-x-4">
                            <?php
            $status_tag = 'negative';
            $label_tag = 'Tutup';

            include __DIR__.'/../components/tag.php'; ?>
                        </div>
                    </div>
                </div>
            </button>
        </form>

        <form action="/form" method="post">
            <input type="hidden" name="heroform" value="KONSELING">
            <?php echo csrf_field(); ?>
            <button type="submit" class="w-full text-left">
                <div class="flex flex-col w-80 h-110 bg-[linear-gradient(180deg,rgba(102,102,102,0)_50.48%,rgba(0,0,0,0.7)_78.85%),url(/images/placeholder.jpg)] bg-cover bg-center p-4 transition-all transform hover:scale-110 hover:outline hover:outline-4 hover:outline-teal-500 hover:shadow-2xl hover:shadow-teal-700/75 shadow-md shadow-gray-500 active:scale-100">
                    <div class="mt-auto">
                        <h3 class="text-4xl  font-bold m-2 font-display-expanded text-white">Konseling</h3>
                        <div class="scale-80 left-0 -translate-x-4">
                            <?php
            $status_tag = 'positive';
            $label_tag = 'Buka';

            include __DIR__.'/../components/tag.php'; ?>
                        </div>
                    </div>
                </div>
            </button>
        </form>
    </div>
</div>

