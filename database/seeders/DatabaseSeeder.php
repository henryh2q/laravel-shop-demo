<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\Page;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(['email' => 'admin@demo.test'], [
            'name' => 'Demo Admin',
            'password' => 'demo1234',
            'is_admin' => true,
        ]);

        $categories = collect([
            ['tea', 'Tea', 'お茶', 'Trà'],
            ['teaware', 'Teaware', '茶器', 'Dụng cụ trà'],
            ['wagashi', 'Wagashi', '和菓子', 'Bánh ngọt Nhật'],
            ['gifts', 'Gifts & café', 'ギフト・カフェ', 'Quà tặng & cà phê'],
        ])->mapWithKeys(fn ($c, $i) => [$c[0] => Category::create([
            'slug' => $c[0], 'name' => ['en' => $c[1], 'ja' => $c[2], 'vi' => $c[3]], 'sort_order' => $i,
        ])]);

        // [category, slug, EN, JA, VI, price (cents), description EN, JA, VI, Unsplash photo, "Photographer|username"]
        $products = [
            ['tea', 'ceremonial-matcha-uji', 'Ceremonial matcha, Uji (30 g)', '宇治抹茶 濃茶用 (30g)', 'Matcha nghi lễ Uji (30 g)', 2800,
                'Stone-ground first-harvest tencha from Uji, Kyoto. Bright green, sweet and full of umami. For koicha and usucha.', '京都・宇治の一番茶を石臼で挽いた抹茶。鮮やかな緑と濃厚な旨み。濃茶・薄茶に。', 'Tencha vụ đầu từ Uji, Kyoto, xay bằng cối đá. Màu xanh tươi, vị ngọt và đậm umami.',
                'photo-1704079698754-5e621edb610b', 'Kairi Kaljo|kairika'],
            ['tea', 'culinary-matcha-100g', 'Everyday matcha for lattes (100 g)', 'ラテ用 抹茶 (100g)', 'Matcha pha latte (100 g)', 2200,
                'A robust blend made for lattes, baking and smoothies.', 'ラテやお菓子作りにぴったりの、しっかりした味わいの抹茶。', 'Matcha đậm vị dùng cho latte, làm bánh và sinh tố.',
                'photo-1771405317898-26a2e16df5fb', 'Madeline Liu|madeline_sd'],
            ['tea', 'genmaicha-toasted-rice', 'Genmaicha with toasted rice', '玄米茶', 'Trà gạo rang Genmaicha', 1400,
                'Sencha blended with roasted brown rice for a nutty, comforting cup.', '煎茶に香ばしい炒り玄米をブレンド。', 'Sencha trộn gạo lứt rang, thơm bùi.',
                'photo-1596235502219-a1c80d8be60c', 'TEAcreativelife │ Soo Chung|teacreativelife'],
            ['tea', 'hojicha-roasted-green-tea', 'Hojicha roasted green tea', 'ほうじ茶', 'Trà rang Hojicha', 1600,
                'Roasted over charcoal for a warm caramel aroma and very little caffeine.', '炭火で焙煎した、香ばしくカフェイン控えめのお茶。', 'Rang trên than hoa, hương caramel ấm, ít caffeine.',
                'photo-1729259586624-fbff74362a5c', 'dasha xue|dashaxue'],
            ['teaware', 'tetsubin-cast-iron-kettle', 'Tetsubin cast iron kettle', '南部鉄瓶', 'Ấm gang Tetsubin', 12800,
                'Hand-cast Nanbu ironware that softens water and keeps tea hot. 0.8 L.', '水をまろやかにする手作りの南部鉄瓶。容量0.8L。', 'Ấm gang Nanbu đúc thủ công, giữ nhiệt tốt. 0,8 lít.',
                'photo-1601043966939-687b89dc6f9c', 'eduardo froza|eduardofroza'],
            ['teaware', 'kyusu-clay-teapot', 'Kyusu clay teapot', '常滑焼 急須', 'Ấm đất Kyusu', 5400,
                'Side-handled Tokoname teapot with a fine built-in clay strainer.', '目の細かい陶製茶こし付きの常滑焼急須。', 'Ấm Tokoname tay cầm ngang, lưới lọc bằng đất nung.',
                'photo-1629440400842-9108c0ccf336', 'jay huang|uiing'],
            ['teaware', 'stoneware-yunomi-set', 'Stoneware yunomi cups (set of 4)', '湯呑み 4客セット', 'Bộ 4 chén trà gốm', 4200,
                'Hand-thrown cups with a soft speckled glaze.', '手びねりの風合いが温かい湯呑み4客。', 'Chén gốm nặn tay, men lấm tấm nhẹ nhàng.',
                'photo-1610701596007-11502861dcfa', 'Vladimir Gladkov|vovkapanda'],
            ['teaware', 'matcha-bowl-chasen-set', 'Matcha bowl & bamboo whisk set', '抹茶碗・茶筅セット', 'Bộ bát và chổi tre pha matcha', 6800,
                'Everything to whisk your first bowl: chawan, 80-prong chasen and scoop.', '抹茶碗、八十本立茶筅、茶杓のセット。', 'Đủ bộ để đánh matcha: bát chawan, chổi tre 80 sợi, thìa.',
                'photo-1565117661210-fd54898de423', 'Alice Pasqual|stri_khedonia'],
            ['wagashi', 'mitarashi-dango', 'Mitarashi dango (5 skewers)', 'みたらし団子 5本', 'Dango Mitarashi (5 xiên)', 1200,
                'Grilled rice dumplings glazed with sweet soy sauce.', '香ばしく焼いた団子に甘辛いみたらし餡。', 'Bánh dày nướng phủ sốt xì dầu ngọt.',
                'photo-1601317266542-4f4abae2cca4', 'Marek Piwnicki|marekpiwnicki'],
            ['wagashi', 'daifuku-mochi-assortment', 'Daifuku mochi assortment', '大福 詰め合わせ', 'Hộp mochi Daifuku', 1800,
                'Soft mochi filled with red bean, matcha and sakura.', 'こしあん・抹茶・桜の大福詰め合わせ。', 'Mochi mềm nhân đậu đỏ, matcha và hoa anh đào.',
                'photo-1635355347994-b79177b77e5c', 'blackieshoot|blackieshoot'],
            ['wagashi', 'dorayaki-6-pack', 'Dorayaki (6 pack)', 'どら焼き 6個入', 'Bánh Dorayaki (6 chiếc)', 1500,
                'Honey pancakes sandwiching smooth azuki bean paste.', 'ふんわり生地に粒あんをたっぷり。', 'Bánh rán mật ong kẹp nhân đậu đỏ.',
                'photo-1626497132810-f38eb29c5385', 'M. Ghufanil Muta Ali|goefan'],
            ['wagashi', 'seasonal-nerikiri-set', 'Seasonal nerikiri set', '季節の練り切り', 'Bánh Nerikiri theo mùa', 3200,
                'Hand-shaped flower sweets made to accompany matcha.', 'お抹茶に合う、職人手作りの上生菓子。', 'Bánh hình hoa nặn tay, dùng kèm matcha.',
                'photo-1627308595229-7830a5c91f9f', 'Vicky Ng|vickyng'],
            ['gifts', 'matcha-white-chocolate-cookies', 'Matcha white chocolate cookies', '抹茶ホワイトチョコクッキー', 'Bánh quy matcha socola trắng', 1100,
                'Chewy cookies baked with our everyday matcha.', '抹茶を練り込んだしっとりクッキー。', 'Bánh quy dẻo nướng cùng matcha.',
                'photo-1757345016219-7b3b8a1b9fba', 'kimia kazemi|kimick'],
            ['gifts', 'matcha-latte-kit', 'Matcha latte kit', '抹茶ラテキット', 'Bộ pha matcha latte', 3400,
                'Latte matcha, a hand whisk and a recipe card in four languages.', 'ラテ用抹茶、ハンドウィスク、レシピカード付き。', 'Matcha latte, dụng cụ đánh và thẻ công thức.',
                'photo-1515823064-d6e0c04616a7', 'Jason Leung|ninjason'],
            ['gifts', 'tea-and-sweets-gift-box', 'Tea & sweets gift box', 'お茶とお菓子のギフトボックス', 'Hộp quà trà và bánh', 5800,
                'Two teas and a selection of wagashi, wrapped in furoshiki.', 'お茶2種と和菓子の詰め合わせ、風呂敷包み。', 'Hai loại trà và bánh wagashi, gói khăn furoshiki.',
                'photo-1696527167862-35c0b8045dc5', 'Christian Laborde|bonbonjapon'],
            ['gifts', 'tea-canister-set', 'Airtight tea canisters (set of 3)', '茶筒 3本セット', 'Bộ 3 hộp đựng trà', 3900,
                'Double-lidded tins that keep leaves fresh.', '二重蓋で茶葉の鮮度を守る茶筒。', 'Hộp thiếc hai nắp giữ trà luôn tươi.',
                'photo-1760602180499-382146d5eb02', 'Erik Esly|lensofbbb'],
        ];

        foreach ($products as $i => [$cat, $slug, $en, $ja, $vi, $price, $den, $dja, $dvi, $photo, $credit]) {
            Product::create([
                'category_id' => $categories[$cat]->id,
                'slug' => $slug,
                'sku' => strtoupper(substr($cat, 0, 3)).'-'.str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT),
                'name' => ['en' => $en, 'ja' => $ja, 'vi' => $vi],
                'description' => ['en' => $den, 'ja' => $dja, 'vi' => $dvi],
                'price' => $price,
                'stock' => $i % 5 === 0 ? 3 : 25 + $i,
                'image_url' => 'https://images.unsplash.com/'.$photo,
                'image_credit' => $credit,
            ]);
        }

        Page::create([
            'slug' => 'about',
            'title' => ['en' => 'About this demo', 'ja' => 'このデモについて', 'vi' => 'Về bản demo này'],
            'body' => [
                'en' => "Chanoma is a fictional Japanese tea shop, built as a portfolio project, built to show how I build Laravel e-commerce. It is not a real shop and it is not client work.\n\nWhat is inside:\n• Laravel + Blade storefront with a session cart and server-side price calculation\n• Stripe Checkout in test mode, with signed webhooks\n• No double charges: every checkout attempt has an idempotency key; repeated submits reuse the same order and Stripe session, and each webhook event is processed once\n• Full and partial refunds from the admin panel, synced back from Stripe webhooks\n• Custom admin panel: orders, products, multilingual CMS pages and a webhook log\n• English, Japanese and Vietnamese content\n\nProduct photos are from Unsplash, credited on each product page. The source code is on GitHub. Admin demo login: admin@demo.test / demo1234.",
                'ja' => "「茶の間」は架空の日本茶ショップで、ポートフォリオ用に制作したデモです。実在の店舗やクライアント案件ではありません。\n\n主な機能：\n• Laravel + Blade のストアフロント、セッションカート、サーバー側での価格計算\n• Stripe Checkout（テストモード）と署名付きWebhook\n• 二重決済の防止：購入手続きごとに冪等キーを発行し、同じ注文・同じStripeセッションを再利用。Webhookイベントは一度だけ処理\n• 管理画面からの全額・一部返金、WebhookでStripeと同期\n• 独自の管理画面：注文、商品、多言語CMSページ、Webhookログ\n• 英語・日本語・ベトナム語対応\n\nソースコードはGitHubで公開しています。管理画面デモ：admin@demo.test / demo1234",
                'vi' => "Chanoma là cửa hàng trà Nhật hư cấu, một dự án portfolio, dựng lên để minh hoạ cách tôi làm thương mại điện tử với Laravel. Đây không phải cửa hàng thật và không phải dự án khách hàng.\n\nBên trong có:\n• Giao diện Laravel + Blade, giỏ hàng theo session, giá tính phía server\n• Stripe Checkout ở chế độ test, webhook có chữ ký\n• Không trừ tiền hai lần: mỗi lần checkout có idempotency key; bấm lại sẽ dùng lại cùng đơn và cùng phiên Stripe, mỗi sự kiện webhook chỉ xử lý một lần\n• Hoàn tiền toàn phần hoặc một phần từ trang quản trị, đồng bộ lại qua webhook\n• Trang quản trị tự viết: đơn hàng, sản phẩm, trang CMS đa ngôn ngữ, log webhook\n• Tiếng Anh, tiếng Nhật, tiếng Việt\n\nMã nguồn trên GitHub. Tài khoản admin demo: admin@demo.test / demo1234.",
            ],
        ]);

        Page::create([
            'slug' => 'shipping',
            'title' => ['en' => 'Shipping (sample page)', 'ja' => '配送について（サンプル）', 'vi' => 'Giao hàng (trang mẫu)'],
            'body' => [
                'en' => 'This is a sample CMS page. Edit it from the admin panel under CMS pages; each language is stored separately.',
                'ja' => 'これはサンプルのCMSページです。管理画面の「CMS pages」から言語ごとに編集できます。',
                'vi' => 'Đây là trang CMS mẫu. Sửa trong trang quản trị, mục CMS pages; mỗi ngôn ngữ lưu riêng.',
            ],
        ]);

        $this->sampleOrders();
    }

    /** A few historical orders so the admin dashboard has something to show. */
    private function sampleOrders(): void
    {
        $samples = [
            ['Yuki Tanaka', 'yuki@example.com', 'ja', Order::PAID, [1, 5], 6],
            ['Minh Nguyen', 'minh@example.com', 'vi', Order::PARTIALLY_REFUNDED, [2, 13], 4],
            ['Emma Wilson', 'emma@example.com', 'en', Order::REFUNDED, [10], 3],
            ['Kenji Sato', 'kenji@example.com', 'ja', Order::PAID, [11, 6, 7], 2],
            ['Lan Tran', 'lan@example.com', 'vi', Order::CANCELED, [15], 1],
        ];

        foreach ($samples as [$name, $email, $locale, $status, $productIds, $daysAgo]) {
            $products = Product::whereIn('id', $productIds)->get();
            $total = $products->sum('price');
            $created = now()->subDays($daysAgo);

            $order = Order::create([
                'number' => 'DS-'.$created->format('ymd').'-'.strtoupper(Str::random(6)),
                'idempotency_key' => (string) Str::uuid(),
                'status' => $status,
                'customer_name' => $name,
                'customer_email' => $email,
                'locale' => $locale,
                'subtotal' => $total,
                'total' => $total,
                'refunded_amount' => match ($status) {
                    Order::REFUNDED => $total,
                    Order::PARTIALLY_REFUNDED => intdiv($total, 2),
                    default => 0,
                },
                'gateway' => 'sample',
                'gateway_session_id' => 'sample_cs_'.Str::random(16),
                'gateway_payment_id' => $status === Order::CANCELED ? null : 'sample_pi_'.Str::random(16),
                'paid_at' => $status === Order::CANCELED ? null : $created,
            ]);
            $order->forceFill(['created_at' => $created, 'updated_at' => $created])->save();

            foreach ($products as $product) {
                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->translate('name', $locale),
                    'unit_price' => $product->price,
                    'quantity' => 1,
                    'line_total' => $product->price,
                ]);
            }

            if ($order->refunded_amount > 0) {
                $order->refunds()->create([
                    'amount' => $order->refunded_amount,
                    'reason' => $status === Order::REFUNDED ? 'Customer changed their mind' : 'Item arrived damaged',
                    'status' => 'succeeded',
                    'gateway_refund_id' => 'sample_re_'.Str::random(16),
                ]);
            }
        }
    }
}
