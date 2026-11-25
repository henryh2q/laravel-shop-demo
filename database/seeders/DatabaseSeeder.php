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
            ['kitchen', 'Kitchen', 'キッチン', 'Nhà bếp'],
            ['stationery', 'Stationery', '文房具', 'Văn phòng phẩm'],
            ['home', 'Home', 'インテリア', 'Nhà cửa'],
            ['outdoor', 'Outdoor', 'アウトドア', 'Dã ngoại'],
        ])->mapWithKeys(fn ($c, $i) => [$c[0] => Category::create([
            'slug' => $c[0], 'name' => ['en' => $c[1], 'ja' => $c[2], 'vi' => $c[3]], 'sort_order' => $i,
        ])]);

        $products = [
            ['kitchen', 'Cast iron teapot', '南部鉄器の急須', 'Ấm trà gang', 4800, 'Hand-finished cast iron teapot with an enamel interior. Holds 650 ml.', '内側ホーロー仕上げの鉄器急須。容量650ml。', 'Ấm trà gang tráng men bên trong, dung tích 650 ml.'],
            ['kitchen', 'Hinoki cutting board', 'ひのきのまな板', 'Thớt gỗ hinoki', 3600, 'Lightweight cypress board that is gentle on knife edges.', '刃あたりのやさしい軽量なひのきのまな板。', 'Thớt gỗ bách nhẹ, êm dao.'],
            ['kitchen', 'Ceramic rice bowl set', '磁器のご飯茶碗セット', 'Bộ bát cơm gốm', 2900, 'Set of two glazed porcelain bowls.', '釉薬仕上げの磁器茶碗2個セット。', 'Bộ 2 bát sứ tráng men.'],
            ['kitchen', 'Bamboo chopsticks (5 pairs)', '竹箸 5膳セット', 'Đũa tre (5 đôi)', 1200, 'Natural bamboo, dishwasher safe.', '天然竹、食洗機対応。', 'Tre tự nhiên, dùng được máy rửa bát.'],
            ['stationery', 'A5 dot grid notebook', 'A5 ドット方眼ノート', 'Sổ chấm A5', 1400, '160 pages of 80 gsm paper with a lay-flat binding.', '80gsm・160ページ、開きやすい製本。', '160 trang giấy 80 gsm, gáy mở phẳng.'],
            ['stationery', 'Brass ballpoint pen', '真鍮ボールペン', 'Bút bi đồng thau', 3200, 'Solid brass body that develops a patina over time.', '使うほど味の出る真鍮ボディ。', 'Thân đồng thau nguyên khối, lên màu theo thời gian.'],
            ['stationery', 'Washi tape set', 'マスキングテープセット', 'Bộ băng dính washi', 900, 'Six rolls of patterned paper tape.', '柄入りマスキングテープ6巻。', '6 cuộn băng dính giấy hoạ tiết.'],
            ['stationery', 'Desk organizer', 'デスクオーガナイザー', 'Khay để bàn', 2500, 'Oak tray with three compartments.', 'オーク材の3分割トレー。', 'Khay gỗ sồi 3 ngăn.'],
            ['home', 'Linen cushion cover', 'リネンのクッションカバー', 'Vỏ gối linen', 2200, 'Stonewashed linen, 45 × 45 cm.', 'ストーンウォッシュ加工のリネン、45×45cm。', 'Vải linen wash đá, 45 × 45 cm.'],
            ['home', 'Soy wax candle', 'ソイキャンドル', 'Nến sáp đậu nành', 1800, 'Yuzu and cedar scent, around 40 hours of burn time.', '柚子とシダーの香り、燃焼時間約40時間。', 'Hương yuzu và tuyết tùng, cháy khoảng 40 giờ.'],
            ['home', 'Wall clock', '壁掛け時計', 'Đồng hồ treo tường', 5600, 'Silent movement, 30 cm walnut face.', 'サイレントムーブメント、30cmウォールナット文字盤。', 'Máy chạy êm, mặt gỗ óc chó 30 cm.'],
            ['home', 'Ceramic vase', '陶器の花瓶', 'Bình gốm', 3900, 'Matte glaze, 22 cm tall.', 'マット釉、高さ22cm。', 'Men mờ, cao 22 cm.'],
            ['outdoor', 'Insulated bottle 500 ml', '保温ボトル 500ml', 'Bình giữ nhiệt 500 ml', 2700, 'Keeps drinks hot for 12 hours.', '12時間保温。', 'Giữ nóng 12 giờ.'],
            ['outdoor', 'Folding camp stool', '折りたたみチェア', 'Ghế xếp dã ngoại', 3400, 'Aluminium frame, 900 g.', 'アルミフレーム、900g。', 'Khung nhôm, nặng 900 g.'],
            ['outdoor', 'Canvas tote bag', '帆布トートバッグ', 'Túi tote canvas', 1900, 'Heavy 12 oz canvas with inner pocket.', '12オンスの厚手帆布、内ポケット付き。', 'Vải canvas 12 oz dày, có túi trong.'],
            ['outdoor', 'LED lantern', 'LEDランタン', 'Đèn lồng LED', 4200, 'Rechargeable, three brightness levels.', '充電式、3段階調光。', 'Sạc lại được, 3 mức sáng.'],
        ];

        foreach ($products as $i => [$cat, $en, $ja, $vi, $price, $den, $dja, $dvi]) {
            Product::create([
                'category_id' => $categories[$cat]->id,
                'slug' => Str::slug($en),
                'sku' => strtoupper(substr($cat, 0, 3)).'-'.str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT),
                'name' => ['en' => $en, 'ja' => $ja, 'vi' => $vi],
                'description' => ['en' => $den, 'ja' => $dja, 'vi' => $dvi],
                'price' => $price,
                'stock' => $i % 5 === 0 ? 3 : 25 + $i,
            ]);
        }

        Page::create([
            'slug' => 'about',
            'title' => ['en' => 'About this demo', 'ja' => 'このデモについて', 'vi' => 'Về bản demo này'],
            'body' => [
                'en' => "This store is a portfolio project, built to show how I build Laravel e-commerce. It is not a real shop and it is not client work.\n\nWhat is inside:\n• Laravel + Blade storefront with a session cart and server-side price calculation\n• Stripe Checkout in test mode, with signed webhooks\n• No double charges: every checkout attempt has an idempotency key; repeated submits reuse the same order and Stripe session, and each webhook event is processed once\n• Full and partial refunds from the admin panel, synced back from Stripe webhooks\n• Custom admin panel: orders, products, multilingual CMS pages and a webhook log\n• English, Japanese and Vietnamese content\n\nThe source code is on GitHub. Admin demo login: admin@demo.test / demo1234.",
                'ja' => "このストアはポートフォリオ用に制作したデモです。実在の店舗やクライアント案件ではありません。\n\n主な機能：\n• Laravel + Blade のストアフロント、セッションカート、サーバー側での価格計算\n• Stripe Checkout（テストモード）と署名付きWebhook\n• 二重決済の防止：購入手続きごとに冪等キーを発行し、同じ注文・同じStripeセッションを再利用。Webhookイベントは一度だけ処理\n• 管理画面からの全額・一部返金、WebhookでStripeと同期\n• 独自の管理画面：注文、商品、多言語CMSページ、Webhookログ\n• 英語・日本語・ベトナム語対応\n\nソースコードはGitHubで公開しています。管理画面デモ：admin@demo.test / demo1234",
                'vi' => "Cửa hàng này là dự án portfolio, dựng lên để minh hoạ cách tôi làm thương mại điện tử với Laravel. Đây không phải cửa hàng thật và không phải dự án khách hàng.\n\nBên trong có:\n• Giao diện Laravel + Blade, giỏ hàng theo session, giá tính phía server\n• Stripe Checkout ở chế độ test, webhook có chữ ký\n• Không trừ tiền hai lần: mỗi lần checkout có idempotency key; bấm lại sẽ dùng lại cùng đơn và cùng phiên Stripe, mỗi sự kiện webhook chỉ xử lý một lần\n• Hoàn tiền toàn phần hoặc một phần từ trang quản trị, đồng bộ lại qua webhook\n• Trang quản trị tự viết: đơn hàng, sản phẩm, trang CMS đa ngôn ngữ, log webhook\n• Tiếng Anh, tiếng Nhật, tiếng Việt\n\nMã nguồn trên GitHub. Tài khoản admin demo: admin@demo.test / demo1234.",
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
