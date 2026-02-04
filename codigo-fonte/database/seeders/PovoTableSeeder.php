<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PovoTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $povos = [
            [
                "nome" => "Aconã",
                "codEtnia" => "001.00"
            ],
            [
                "nome" => "Aikanã",
                "codEtnia" => "002.00"
            ],
            [
                "nome" => "Aikewara",
                "codEtnia" => "003.00"
            ],
            [
                "nome" => "Aimore",
                "codEtnia" => "004.00"
            ],
            [
                "nome" => "Ajuru",
                "codEtnia" => "005.00"
            ],
            [
                "nome" => "Akuntsú",
                "codEtnia" => "006.00"
            ],
            [
                "nome" => "Amanayé",
                "codEtnia" => "007.00"
            ],
            [
                "nome" => "Amondáwa",
                "codEtnia" => "008.00"
            ],
            [
                "nome" => "Anacé",
                "codEtnia" => "009.00"
            ],
            [
                "nome" => "Anambé",
                "codEtnia" => "010.00"
            ],
            [
                "nome" => "Apalaí",
                "codEtnia" => "011.00"
            ],
            [
                "nome" => "Apiaká",
                "codEtnia" => "012.00"
            ],
            [
                "nome" => "Apinayé",
                "codEtnia" => "013.00"
            ],
            [
                "nome" => "Apolima-arara",
                "codEtnia" => "014.00"
            ],
            [
                "nome" => "Apurinã",
                "codEtnia" => "015.00"
            ],
            [
                "nome" => "Aranã",
                "codEtnia" => "016.00"
            ],
            [
                "nome" => "Arapáso",
                "codEtnia" => "017.00"
            ],
            [
                "nome" => "Arapiun",
                "codEtnia" => "018.00"
            ],
            [
                "nome" => "Arara de rondônia",
                "codEtnia" => "019.00"
            ],
            [
                "nome" => "Arara do acre",
                "codEtnia" => "020.00"
            ],
            [
                "nome" => "Arara do aripuanã",
                "codEtnia" => "021.00"
            ],
            [
                "nome" => "Arara do pará",
                "codEtnia" => "022.00"
            ],
            [
                "nome" => "Arara vermelha",
                "codEtnia" => "023.00"
            ],
            [
                "nome" => "Araweté",
                "codEtnia" => "024.00"
            ],
            [
                "nome" => "Arikapú",
                "codEtnia" => "025.00"
            ],
            [
                "nome" => "Arikén",
                "codEtnia" => "026.00"
            ],
            [
                "nome" => "Arikosé",
                "codEtnia" => "027.00"
            ],
            [
                "nome" => "Aruá",
                "codEtnia" => "028.00"
            ],
            [
                "nome" => "Ashaninka",
                "codEtnia" => "029.00"
            ],
            [
                "nome" => "Asurini do tocantins",
                "codEtnia" => "030.00"
            ],
            [
                "nome" => "Asurini do xingu",
                "codEtnia" => "031.00"
            ],
            [
                "nome" => "Atikum",
                "codEtnia" => "032.00"
            ],
            [
                "nome" => "Ava-canoeiro",
                "codEtnia" => "033.00"
            ],
            [
                "nome" => "Awá guajá",
                "codEtnia" => "034.00"
            ],
            [
                "nome" => "Aweti",
                "codEtnia" => "035.00"
            ],
            [
                "nome" => "Baenã",
                "codEtnia" => "036.00"
            ],
            [
                "nome" => "Bakairí",
                "codEtnia" => "037.00"
            ],
            [
                "nome" => "Banawa",
                "codEtnia" => "038.00"
            ],
            [
                "nome" => "Baniwa",
                "codEtnia" => "039.00"
            ],
            [
                "nome" => "Bará",
                "codEtnia" => "040.00"
            ],
            [
                "nome" => "Barasána",
                "codEtnia" => "041.00"
            ],
            [
                "nome" => "Baré",
                "codEtnia" => "042.00"
            ],
            [
                "nome" => "Bóra",
                "codEtnia" => "043.00"
            ],
            [
                "nome" => "Borari",
                "codEtnia" => "044.00"
            ],
            [
                "nome" => "Bororo",
                "codEtnia" => "045.00"
            ],
            [
                "nome" => "Botocudo",
                "codEtnia" => "046.00"
            ],
            [
                "nome" => "Caboclos do assu",
                "codEtnia" => "047.00"
            ],
            [
                "nome" => "Catokin",
                "codEtnia" => "048.00"
            ],
            [
                "nome" => "Chamakóko",
                "codEtnia" => "049.00"
            ],
            [
                "nome" => "Charrua",
                "codEtnia" => "050.00"
            ],
            [
                "nome" => "Chiquitáno",
                "codEtnia" => "051.00"
            ],
            [
                "nome" => "Cinta larga",
                "codEtnia" => "052.00"
            ],
            [
                "nome" => "Dâw",
                "codEtnia" => "053.00"
            ],
            [
                "nome" => "Dení",
                "codEtnia" => "054.00"
            ],
            [
                "nome" => "Desána",
                "codEtnia" => "055.00"
            ],
            [
                "nome" => "Diahói",
                "codEtnia" => "056.00"
            ],
            [
                "nome" => "Djeoromitxí - jabutí",
                "codEtnia" => "057.00"
            ],
            [
                "nome" => "Enawenê-nawê",
                "codEtnia" => "058.00"
            ],
            [
                "nome" => "E-ñepá",
                "codEtnia" => "059.00"
            ],
            [
                "nome" => "Fulni-ô",
                "codEtnia" => "060.00"
            ],
            [
                "nome" => "Galibi do oiapoque",
                "codEtnia" => "061.00"
            ],
            [
                "nome" => "Galibí marwórno",
                "codEtnia" => "062.00"
            ],
            [
                "nome" => "Gamela",
                "codEtnia" => "063.00"
            ],
            [
                "nome" => "Gavião de rondônia",
                "codEtnia" => "064.00"
            ],
            [
                "nome" => "Gavião krikatejê",
                "codEtnia" => "065.00"
            ],
            [
                "nome" => "Gavião parkatejê",
                "codEtnia" => "066.00"
            ],
            [
                "nome" => "Gavião pukobiê",
                "codEtnia" => "067.00"
            ],
            [
                "nome" => "Guaikurú",
                "codEtnia" => "068.00"
            ],
            [
                "nome" => "Guajá",
                "codEtnia" => "069.00"
            ],
            [
                "nome" => "Guajajara",
                "codEtnia" => "070.00"
            ],
            [
                "nome" => "Guaraní",
                "codEtnia" => "071.00"
            ],
            [
                "nome" => "Guarani kaiowá",
                "codEtnia" => "072.00"
            ],
            [
                "nome" => "Guarani mbya",
                "codEtnia" => "073.00"
            ],
            [
                "nome" => "Guarani nhandeva",
                "codEtnia" => "074.00"
            ],
            [
                "nome" => "Guarasugwe",
                "codEtnia" => "075.00"
            ],
            [
                "nome" => "Guató",
                "codEtnia" => "076.00"
            ],
            [
                "nome" => "Himarimã",
                "codEtnia" => "077.00"
            ],
            [
                "nome" => "Hixkaryána",
                "codEtnia" => "078.00"
            ],
            [
                "nome" => "Hupd'äh",
                "codEtnia" => "079.00"
            ],
            [
                "nome" => "Ikpeng",
                "codEtnia" => "080.00"
            ],
            [
                "nome" => "Ingarikó",
                "codEtnia" => "081.00"
            ],
            [
                "nome" => "Irántxe",
                "codEtnia" => "082.00"
            ],
            [
                "nome" => "Issé",
                "codEtnia" => "083.00"
            ],
            [
                "nome" => "Jamamadí",
                "codEtnia" => "084.00"
            ],
            [
                "nome" => "Jaraqui",
                "codEtnia" => "085.00"
            ],
            [
                "nome" => "Jarawára",
                "codEtnia" => "086.00"
            ],
            [
                "nome" => "Jaricuna",
                "codEtnia" => "087.00"
            ],
            [
                "nome" => "Javaé",
                "codEtnia" => "088.00"
            ],
            [
                "nome" => "Jenipapo-kanindé",
                "codEtnia" => "089.00"
            ],
            [
                "nome" => "Jeripancó",
                "codEtnia" => "090.00"
            ],
            [
                "nome" => "Juma",
                "codEtnia" => "091.00"
            ],
            [
                "nome" => "Juruna",
                "codEtnia" => "092.00"
            ],
            [
                "nome" => "Ka'apor",
                "codEtnia" => "093.00"
            ],
            [
                "nome" => "Kadiwéu",
                "codEtnia" => "094.00"
            ],
            [
                "nome" => "Kaeté",
                "codEtnia" => "095.00"
            ],
            [
                "nome" => "Kahyana",
                "codEtnia" => "096.00"
            ],
            [
                "nome" => "Kaiabi",
                "codEtnia" => "097.00"
            ],
            [
                "nome" => "Kaimbé",
                "codEtnia" => "098.00"
            ],
            [
                "nome" => "Kaingang",
                "codEtnia" => "099.00"
            ],
            [
                "nome" => "Kaixana",
                "codEtnia" => "100.00"
            ],
            [
                "nome" => "Kalabaça",
                "codEtnia" => "101.00"
            ],
            [
                "nome" => "Kalankó",
                "codEtnia" => "102.00"
            ],
            [
                "nome" => "Kalapalo",
                "codEtnia" => "103.00"
            ],
            [
                "nome" => "Kamakã",
                "codEtnia" => "104.00"
            ],
            [
                "nome" => "Kamayurá",
                "codEtnia" => "105.00"
            ],
            [
                "nome" => "Kamba",
                "codEtnia" => "106.00"
            ],
            [
                "nome" => "Kambéba",
                "codEtnia" => "107.00"
            ],
            [
                "nome" => "Kambiwá",
                "codEtnia" => "108.00"
            ],
            [
                "nome" => "Kambiwá-pipipã",
                "codEtnia" => "109.00"
            ],
            [
                "nome" => "Kampé",
                "codEtnia" => "110.00"
            ],
            [
                "nome" => "Kanamanti",
                "codEtnia" => "111.00"
            ],
            [
                "nome" => "Kanamarí",
                "codEtnia" => "112.00"
            ],
            [
                "nome" => "Kanela",
                "codEtnia" => "113.00"
            ],
            [
                "nome" => "Kanela apaniekra",
                "codEtnia" => "114.00"
            ],
            [
                "nome" => "Kanela rankocamekra",
                "codEtnia" => "115.00"
            ],
            [
                "nome" => "Kanindé",
                "codEtnia" => "116.00"
            ],
            [
                "nome" => "Kanoé",
                "codEtnia" => "117.00"
            ],
            [
                "nome" => "Kantaruré",
                "codEtnia" => "118.00"
            ],
            [
                "nome" => "Kapinawá",
                "codEtnia" => "119.00"
            ],
            [
                "nome" => "Karafawyana",
                "codEtnia" => "120.00"
            ],
            [
                "nome" => "Karajá",
                "codEtnia" => "121.00"
            ],
            [
                "nome" => "Karapanã",
                "codEtnia" => "122.00"
            ],
            [
                "nome" => "Karapotó",
                "codEtnia" => "123.00"
            ],
            [
                "nome" => "Karijó",
                "codEtnia" => "124.00"
            ],
            [
                "nome" => "Karipuna",
                "codEtnia" => "125.00"
            ],
            [
                "nome" => "Karipúna do amapá",
                "codEtnia" => "126.00"
            ],
            [
                "nome" => "Kariri",
                "codEtnia" => "127.00"
            ],
            [
                "nome" => "Kariri-xocó",
                "codEtnia" => "128.00"
            ],
            [
                "nome" => "Karitiana",
                "codEtnia" => "129.00"
            ],
            [
                "nome" => "Kassupá",
                "codEtnia" => "130.00"
            ],
            [
                "nome" => "Katawixí",
                "codEtnia" => "131.00"
            ],
            [
                "nome" => "Katukina",
                "codEtnia" => "132.00"
            ],
            [
                "nome" => "Katukina do acre",
                "codEtnia" => "133.00"
            ],
            [
                "nome" => "Katwena",
                "codEtnia" => "134.00"
            ],
            [
                "nome" => "Katxuyana",
                "codEtnia" => "135.00"
            ],
            [
                "nome" => "Kaxarari",
                "codEtnia" => "136.00"
            ],
            [
                "nome" => "Kaxinawá",
                "codEtnia" => "137.00"
            ],
            [
                "nome" => "Kaxixó",
                "codEtnia" => "138.00"
            ],
            [
                "nome" => "Kayapó",
                "codEtnia" => "139.00"
            ],
            [
                "nome" => "Kararao",
                "codEtnia" => "139.01"
            ],
            [
                "nome" => "Mebêngôkre kayapó",
                "codEtnia" => "139.02"
            ],
            [
                "nome" => "Menkrangnoti",
                "codEtnia" => "139.03"
            ],
            [
                "nome" => "Mentuktire",
                "codEtnia" => "139.04"
            ],
            [
                "nome" => "Xikrin (mebengôkre)",
                "codEtnia" => "139.05"
            ],
            [
                "nome" => "Kayuisiana",
                "codEtnia" => "140.00"
            ],
            [
                "nome" => "Kinikinau",
                "codEtnia" => "141.00"
            ],
            [
                "nome" => "Kiriri",
                "codEtnia" => "142.00"
            ],
            [
                "nome" => "Kisêdjê",
                "codEtnia" => "143.00"
            ],
            [
                "nome" => "Koiupanká",
                "codEtnia" => "144.00"
            ],
            [
                "nome" => "Kokama",
                "codEtnia" => "145.00"
            ],
            [
                "nome" => "Kokuiregatejê",
                "codEtnia" => "146.00"
            ],
            [
                "nome" => "Kontanawá",
                "codEtnia" => "147.00"
            ],
            [
                "nome" => "Korubo",
                "codEtnia" => "148.00"
            ],
            [
                "nome" => "Krahô",
                "codEtnia" => "149.00"
            ],
            [
                "nome" => "Krahô-kanela",
                "codEtnia" => "150.00"
            ],
            [
                "nome" => "Krenák",
                "codEtnia" => "151.00"
            ],
            [
                "nome" => "Krenyê",
                "codEtnia" => "152.00"
            ],
            [
                "nome" => "Krikati",
                "codEtnia" => "153.00"
            ],
            [
                "nome" => "Kubeo",
                "codEtnia" => "154.00"
            ],
            [
                "nome" => "Kuikuro",
                "codEtnia" => "155.00"
            ],
            [
                "nome" => "Kujubim",
                "codEtnia" => "156.00"
            ],
            [
                "nome" => "Kulina madijá",
                "codEtnia" => "157.00"
            ],
            [
                "nome" => "Kulina páno",
                "codEtnia" => "158.00"
            ],
            [
                "nome" => "Kumaruara",
                "codEtnia" => "159.00"
            ],
            [
                "nome" => "Kuntanawa",
                "codEtnia" => "160.00"
            ],
            [
                "nome" => "Kuripako",
                "codEtnia" => "161.00"
            ],
            [
                "nome" => "Kuruáya",
                "codEtnia" => "162.00"
            ],
            [
                "nome" => "Kwazá",
                "codEtnia" => "163.00"
            ],
            [
                "nome" => "Laiana",
                "codEtnia" => "164.00"
            ],
            [
                "nome" => "Makú",
                "codEtnia" => "165.00"
            ],
            [
                "nome" => "Makúna",
                "codEtnia" => "166.00"
            ],
            [
                "nome" => "Makuráp",
                "codEtnia" => "167.00"
            ],
            [
                "nome" => "Makuxí",
                "codEtnia" => "168.00"
            ],
            [
                "nome" => "Manao",
                "codEtnia" => "169.00"
            ],
            [
                "nome" => "Manchineri",
                "codEtnia" => "170.00"
            ],
            [
                "nome" => "Maragua",
                "codEtnia" => "171.00"
            ],
            [
                "nome" => "Marimã",
                "codEtnia" => "172.00"
            ],
            [
                "nome" => "Marúbo",
                "codEtnia" => "173.00"
            ],
            [
                "nome" => "Matipú",
                "codEtnia" => "174.00"
            ],
            [
                "nome" => "Matís",
                "codEtnia" => "175.00"
            ],
            [
                "nome" => "Matsés",
                "codEtnia" => "176.00"
            ],
            [
                "nome" => "Mawayána",
                "codEtnia" => "177.00"
            ],
            [
                "nome" => "Maxakali",
                "codEtnia" => "178.00"
            ],
            [
                "nome" => "Maya",
                "codEtnia" => "179.00"
            ],
            [
                "nome" => "Maytapu",
                "codEtnia" => "180.00"
            ],
            [
                "nome" => "Mehináku",
                "codEtnia" => "181.00"
            ],
            [
                "nome" => "Menkü",
                "codEtnia" => "182.00"
            ],
            [
                "nome" => "Migueléno",
                "codEtnia" => "183.00"
            ],
            [
                "nome" => "Miránha",
                "codEtnia" => "184.00"
            ],
            [
                "nome" => "Mirititapuia",
                "codEtnia" => "185.00"
            ],
            [
                "nome" => "Mucurim",
                "codEtnia" => "186.00"
            ],
            [
                "nome" => "Munduruku",
                "codEtnia" => "187.00"
            ],
            [
                "nome" => "Munduruku carapreta",
                "codEtnia" => "188.00"
            ],
            [
                "nome" => "Múra",
                "codEtnia" => "189.00"
            ],
            [
                "nome" => "Nadëb",
                "codEtnia" => "190.00"
            ],
            [
                "nome" => "Nahukuá",
                "codEtnia" => "191.00"
            ],
            [
                "nome" => "Nambikwára",
                "codEtnia" => "192.00"
            ],
            [
                "nome" => "Alaketesu",
                "codEtnia" => "192.01"
            ],
            [
                "nome" => "Alantesu",
                "codEtnia" => "192.02"
            ],
            [
                "nome" => "Hahaintesu",
                "codEtnia" => "192.03"
            ],
            [
                "nome" => "Halotesu",
                "codEtnia" => "192.04"
            ],
            [
                "nome" => "Kithaulu",
                "codEtnia" => "192.05"
            ],
            [
                "nome" => "Lakondê",
                "codEtnia" => "192.06"
            ],
            [
                "nome" => "Latundê",
                "codEtnia" => "192.07"
            ],
            [
                "nome" => "Mamaindê",
                "codEtnia" => "192.08"
            ],
            [
                "nome" => "Manduka",
                "codEtnia" => "192.09"
            ],
            [
                "nome" => "Negarotê",
                "codEtnia" => "192.10"
            ],
            [
                "nome" => "Sabanê",
                "codEtnia" => "192.11"
            ],
            [
                "nome" => "Sarare",
                "codEtnia" => "192.12"
            ],
            [
                "nome" => "Sawentesu",
                "codEtnia" => "192.13"
            ],
            [
                "nome" => "Tawandê",
                "codEtnia" => "192.14"
            ],
            [
                "nome" => "Waikisu",
                "codEtnia" => "192.15"
            ],
            [
                "nome" => "Wakalitesu",
                "codEtnia" => "192.16"
            ],
            [
                "nome" => "Wasusu",
                "codEtnia" => "192.17"
            ],
            [
                "nome" => "Nawa",
                "codEtnia" => "193.00"
            ],
            [
                "nome" => "Noke koi",
                "codEtnia" => "194.00"
            ],
            [
                "nome" => "Nukiní",
                "codEtnia" => "195.00"
            ],
            [
                "nome" => "Ofayé",
                "codEtnia" => "196.00"
            ],
            [
                "nome" => "Oro win",
                "codEtnia" => "197.00"
            ],
            [
                "nome" => "Paiaku",
                "codEtnia" => "198.00"
            ],
            [
                "nome" => "Pakaa nova",
                "codEtnia" => "199.00"
            ],
            [
                "nome" => "Oro at",
                "codEtnia" => "199.01"
            ],
            [
                "nome" => "Oro eo",
                "codEtnia" => "199.02"
            ],
            [
                "nome" => "Oro jowin",
                "codEtnia" => "199.03"
            ],
            [
                "nome" => "Oro mon",
                "codEtnia" => "199.04"
            ],
            [
                "nome" => "Oro náo",
                "codEtnia" => "199.05"
            ],
            [
                "nome" => "Oro wam",
                "codEtnia" => "199.06"
            ],
            [
                "nome" => "Oro waram",
                "codEtnia" => "199.07"
            ],
            [
                "nome" => "Oro waram xijein",
                "codEtnia" => "199.08"
            ],
            [
                "nome" => "Palikur",
                "codEtnia" => "200.00"
            ],
            [
                "nome" => "Panará",
                "codEtnia" => "201.00"
            ],
            [
                "nome" => "Pankaiuká",
                "codEtnia" => "202.00"
            ],
            [
                "nome" => "Pankará",
                "codEtnia" => "203.00"
            ],
            [
                "nome" => "Pankará da aldeia serrote dos campos",
                "codEtnia" => "204.00"
            ],
            [
                "nome" => "Pankararé",
                "codEtnia" => "205.00"
            ],
            [
                "nome" => "Pankararú",
                "codEtnia" => "206.00"
            ],
            [
                "nome" => "Pankararú - karuazu",
                "codEtnia" => "207.00"
            ],
            [
                "nome" => "Pankaru",
                "codEtnia" => "208.00"
            ],
            [
                "nome" => "Papavó",
                "codEtnia" => "209.00"
            ],
            [
                "nome" => "Parakanã",
                "codEtnia" => "210.00"
            ],
            [
                "nome" => "Paresí",
                "codEtnia" => "211.00"
            ],
            [
                "nome" => "Parintintim",
                "codEtnia" => "212.00"
            ],
            [
                "nome" => "Pataxó",
                "codEtnia" => "213.00"
            ],
            [
                "nome" => "Pataxo há-há-há",
                "codEtnia" => "214.00"
            ],
            [
                "nome" => "Paumarí",
                "codEtnia" => "215.00"
            ],
            [
                "nome" => "Paumelenho",
                "codEtnia" => "216.00"
            ],
            [
                "nome" => "Payayá",
                "codEtnia" => "217.00"
            ],
            [
                "nome" => "Pipipã",
                "codEtnia" => "218.00"
            ],
            [
                "nome" => "Pirahã",
                "codEtnia" => "219.00"
            ],
            [
                "nome" => "Piratapuya",
                "codEtnia" => "220.00"
            ],
            [
                "nome" => "Piri-piri",
                "codEtnia" => "221.00"
            ],
            [
                "nome" => "Pitaguari",
                "codEtnia" => "222.00"
            ],
            [
                "nome" => "Potiguara",
                "codEtnia" => "223.00"
            ],
            [
                "nome" => "Povo do xinane",
                "codEtnia" => "224.00"
            ],
            [
                "nome" => "Poyanáwa",
                "codEtnia" => "225.00"
            ],
            [
                "nome" => "Puri",
                "codEtnia" => "226.00"
            ],
            [
                "nome" => "Puroborá",
                "codEtnia" => "227.00"
            ],
            [
                "nome" => "Rikbaktsa",
                "codEtnia" => "228.00"
            ],
            [
                "nome" => "Sakurabiat",
                "codEtnia" => "229.00"
            ],
            [
                "nome" => "Salamãy",
                "codEtnia" => "230.00"
            ],
            [
                "nome" => "Sapará",
                "codEtnia" => "231.00"
            ],
            [
                "nome" => "Sateré-mawé",
                "codEtnia" => "232.00"
            ],
            [
                "nome" => "Shanenáwa",
                "codEtnia" => "233.00"
            ],
            [
                "nome" => "Sikiyana",
                "codEtnia" => "234.00"
            ],
            [
                "nome" => "Siriano",
                "codEtnia" => "235.00"
            ],
            [
                "nome" => "Suruí de rondônia",
                "codEtnia" => "236.00"
            ],
            [
                "nome" => "Suruwaha",
                "codEtnia" => "237.00"
            ],
            [
                "nome" => "Tabajara",
                "codEtnia" => "238.00"
            ],
            [
                "nome" => "Tamoio",
                "codEtnia" => "239.00"
            ],
            [
                "nome" => "Tapajós",
                "codEtnia" => "240.00"
            ],
            [
                "nome" => "Tapayuna",
                "codEtnia" => "241.00"
            ],
            [
                "nome" => "Tapeba",
                "codEtnia" => "242.00"
            ],
            [
                "nome" => "Tapirapé",
                "codEtnia" => "243.00"
            ],
            [
                "nome" => "Tapiuns",
                "codEtnia" => "244.00"
            ],
            [
                "nome" => "Tapuia",
                "codEtnia" => "245.00"
            ],
            [
                "nome" => "Tariana",
                "codEtnia" => "246.00"
            ],
            [
                "nome" => "Taurepang",
                "codEtnia" => "247.00"
            ],
            [
                "nome" => "Tembé",
                "codEtnia" => "248.00"
            ],
            [
                "nome" => "Tenetehara",
                "codEtnia" => "249.00"
            ],
            [
                "nome" => "Tenharim",
                "codEtnia" => "250.00"
            ],
            [
                "nome" => "Terena",
                "codEtnia" => "251.00"
            ],
            [
                "nome" => "Tikúna",
                "codEtnia" => "252.00"
            ],
            [
                "nome" => "Timbira",
                "codEtnia" => "253.00"
            ],
            [
                "nome" => "Tingui-botó",
                "codEtnia" => "254.00"
            ],
            [
                "nome" => "Tiriyó",
                "codEtnia" => "255.00"
            ],
            [
                "nome" => "Torá",
                "codEtnia" => "256.00"
            ],
            [
                "nome" => "Tremembé",
                "codEtnia" => "257.00"
            ],
            [
                "nome" => "Truká",
                "codEtnia" => "258.00"
            ],
            [
                "nome" => "Trumái",
                "codEtnia" => "259.00"
            ],
            [
                "nome" => "Tsohom djapa",
                "codEtnia" => "260.00"
            ],
            [
                "nome" => "Tukano",
                "codEtnia" => "261.00"
            ],
            [
                "nome" => "Tükuna",
                "codEtnia" => "262.00"
            ],
            [
                "nome" => "Tumbalalá",
                "codEtnia" => "263.00"
            ],
            [
                "nome" => "Tupaiu",
                "codEtnia" => "264.00"
            ],
            [
                "nome" => "Tuparí",
                "codEtnia" => "265.00"
            ],
            [
                "nome" => "Tupi",
                "codEtnia" => "266.00"
            ],
            [
                "nome" => "Tupi-guarani",
                "codEtnia" => "267.00"
            ],
            [
                "nome" => "Tupinambá",
                "codEtnia" => "268.00"
            ],
            [
                "nome" => "Tupinambaraná",
                "codEtnia" => "269.00"
            ],
            [
                "nome" => "Tupiniquim",
                "codEtnia" => "270.00"
            ],
            [
                "nome" => "Turiwára",
                "codEtnia" => "271.00"
            ],
            [
                "nome" => "Tuxá",
                "codEtnia" => "272.00"
            ],
            [
                "nome" => "Tuxi",
                "codEtnia" => "273.00"
            ],
            [
                "nome" => "Tuyúca",
                "codEtnia" => "274.00"
            ],
            [
                "nome" => "Umutina",
                "codEtnia" => "275.00"
            ],
            [
                "nome" => "Urucú",
                "codEtnia" => "276.00"
            ],
            [
                "nome" => "Uru-eu-wau-wau",
                "codEtnia" => "277.00"
            ],
            [
                "nome" => "Uru-pa-in",
                "codEtnia" => "278.00"
            ],
            [
                "nome" => "Waiãpy",
                "codEtnia" => "279.00"
            ],
            [
                "nome" => "Waimiri atroari",
                "codEtnia" => "280.00"
            ],
            [
                "nome" => "Wanana",
                "codEtnia" => "281.00"
            ],
            [
                "nome" => "Wapixana",
                "codEtnia" => "282.00"
            ],
            [
                "nome" => "Warao",
                "codEtnia" => "283.00"
            ],
            [
                "nome" => "Warekena",
                "codEtnia" => "284.00"
            ],
            [
                "nome" => "Waripi",
                "codEtnia" => "285.00"
            ],
            [
                "nome" => "Wassú",
                "codEtnia" => "286.00"
            ],
            [
                "nome" => "Wauja",
                "codEtnia" => "287.00"
            ],
            [
                "nome" => "Wayana",
                "codEtnia" => "288.00"
            ],
            [
                "nome" => "Witóto",
                "codEtnia" => "289.00"
            ],
            [
                "nome" => "Xacriabá",
                "codEtnia" => "290.00"
            ],
            [
                "nome" => "Xambioá",
                "codEtnia" => "291.00"
            ],
            [
                "nome" => "Xavante",
                "codEtnia" => "292.00"
            ],
            [
                "nome" => "Xerente",
                "codEtnia" => "293.00"
            ],
            [
                "nome" => "Xereu",
                "codEtnia" => "294.00"
            ],
            [
                "nome" => "Xetá",
                "codEtnia" => "295.00"
            ],
            [
                "nome" => "Xipáya",
                "codEtnia" => "296.00"
            ],
            [
                "nome" => "Xocó",
                "codEtnia" => "297.00"
            ],
            [
                "nome" => "Xokléng",
                "codEtnia" => "298.00"
            ],
            [
                "nome" => "Xucuru",
                "codEtnia" => "299.00"
            ],
            [
                "nome" => "Xukuru-kariri",
                "codEtnia" => "300.00"
            ],
            [
                "nome" => "Yaipiyana",
                "codEtnia" => "301.00"
            ],
            [
                "nome" => "Yamináwa",
                "codEtnia" => "302.00"
            ],
            [
                "nome" => "Yanomami",
                "codEtnia" => "303.00"
            ],
            [
                "nome" => "Ninám",
                "codEtnia" => "303.01"
            ],
            [
                "nome" => "Sanumá",
                "codEtnia" => "303.02"
            ],
            [
                "nome" => "Xiriana",
                "codEtnia" => "303.03"
            ],
            [
                "nome" => "Yanomán",
                "codEtnia" => "303.04"
            ],
            [
                "nome" => "Yawalapití",
                "codEtnia" => "304.00"
            ],
            [
                "nome" => "Yawanawá",
                "codEtnia" => "305.00"
            ],
            [
                "nome" => "Ye'kuana",
                "codEtnia" => "306.00"
            ],
            [
                "nome" => "Yudjá",
                "codEtnia" => "307.00"
            ],
            [
                "nome" => "Yuhúp",
                "codEtnia" => "308.00"
            ],
            [
                "nome" => "Yurutí",
                "codEtnia" => "309.00"
            ],
            [
                "nome" => "Zo'é",
                "codEtnia" => "310.00"
            ],
            [
                "nome" => "Zoró",
                "codEtnia" => "311.00"
            ],
            [
                "nome" => "Isolados",
                "codEtnia" => ""
            ],
            [
                "nome" => "Kaxuyana",
                "codEtnia" => ""
            ],
            [
                "nome" => "Naravute",
                "codEtnia" => ""
            ],
            [
                "nome" => "Zuruahã",
                "codEtnia" => ""
            ]
        ];

        DB::table('povo')->insert($povos);
    }
}
