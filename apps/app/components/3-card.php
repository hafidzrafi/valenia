<div class="flex justify-center items-center min-h-screen">
    <div class="flex space-x-8">

        <form action="/form" method="get">
            <input type="hidden" name="heroform" value="Poli Umum">
            <button type="submit" class="w-full text-left">
                <div class="flex flex-col w-48 h-64 bg-[linear-gradient(to_bottom,rgba(0,0,0,0.0),rgba(0,0,0,0.8)),url(/images/placeholder.jpg)] bg-cover bg-center smooth-corners-lg rounded-[32px]  p-4 transition-all transform hover:scale-110 hover:outline hover:outline-3 hover:outline-teal-500 hover:shadow-2xl hover:shadow-gray-500/75 active:scale-100">
                    <div class="mt-auto">
                        <h3 class="text-[24px] font-bold text-white">Poli Umum</h3>
                        <div class="scale-80 left-0 -translate-x-4">
                            <?php
                            $status_tag = ("positive");
                            $label_tag = ("Buka");
                            include __DIR__ . '/../components/tag.php' ?>
                        </div>
                    </div>
                </div>
            </button>
        </form>

        <form action="/form" method="get">
            <input type="hidden" name="heroform" value="Poli Gigi">
            <button type="submit" class="w-full text-left">
                <div class="flex flex-col w-48 h-64 bg-[linear-gradient(to_bottom,rgba(0,0,0,0.0),rgba(0,0,0,0.8)),url(/images/placeholder.jpg)] bg-cover bg-center smooth-corners-lg rounded-[32px]  p-4 transition-all transform hover:scale-110 hover:outline hover:outline-3 hover:outline-teal-500 hover:shadow-2xl hover:shadow-gray-500/75 active:scale-100">
                    <div class="mt-auto">
                        <h3 class="text-[24px] font-bold text-white">Poli Gigi</h3>
                        <div class="scale-80 left-0 -translate-x-4">
                            <?php
                            $status_tag = ("negative");
                            $label_tag = ("Tutup");
                            include __DIR__ . '/../components/tag.php' ?>
                        </div>
                    </div>
                </div>
            </button>
        </form>

        <form action="/form" method="get">
            <input type="hidden" name="heroform" value="Konseling">
            <button type="submit" class="w-full text-left">
                <div class="flex flex-col w-48 h-64 bg-[linear-gradient(to_bottom,rgba(0,0,0,0.0),rgba(0,0,0,0.8)),url(/images/placeholder.jpg)] bg-cover bg-center smooth-corners-lg rounded-[32px]  p-4 transition-all transform hover:scale-110 hover:outline hover:outline-3 hover:outline-teal-500 hover:shadow-2xl hover:shadow-gray-500/75 active:scale-100">
                    <div class="mt-auto">
                        <h3 class="text-[24px] font-bold text-white">Konseling</h3>
                        <div class="scale-80 left-0 -translate-x-4">
                            <?php
                            $status_tag = ("positive");
                            $label_tag = ("Buka");
                            include __DIR__ . '/../components/tag.php' ?>
                        </div>
                    </div>
                </div>
            </button>
        </form>
    </div>
</div>

<?php



?>