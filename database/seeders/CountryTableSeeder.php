<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('Country')->truncate();
        $Countries = array(
            array('CountryId' => 1, 'CountryCode' => 'AF', 'CountryName' => "Afghanistan", 'PhoneCode' => 93),
            array('CountryId' => 2, 'CountryCode' => 'AL', 'CountryName' => "Albania", 'PhoneCode' => 355),
            array('CountryId' => 3, 'CountryCode' => 'DZ', 'CountryName' => "Algeria", 'PhoneCode' => 213),
            array('CountryId' => 4, 'CountryCode' => 'AS', 'CountryName' => "American Samoa", 'PhoneCode' => 1684),
            array('CountryId' => 5, 'CountryCode' => 'AD', 'CountryName' => "Andorra", 'PhoneCode' => 376),
            array('CountryId' => 6, 'CountryCode' => 'AO', 'CountryName' => "Angola", 'PhoneCode' => 244),
            array('CountryId' => 7, 'CountryCode' => 'AI', 'CountryName' => "Anguilla", 'PhoneCode' => 1264),
            array('CountryId' => 8, 'CountryCode' => 'AQ', 'CountryName' => "Antarctica", 'PhoneCode' => 0),
            array('CountryId' => 9, 'CountryCode' => 'AG', 'CountryName' => "Antigua And Barbuda", 'PhoneCode' => 1268),
            array('CountryId' => 10, 'CountryCode' => 'AR', 'CountryName' => "Argentina", 'PhoneCode' => 54),
            array('CountryId' => 11, 'CountryCode' => 'AM', 'CountryName' => "Armenia", 'PhoneCode' => 374),
            array('CountryId' => 12, 'CountryCode' => 'AW', 'CountryName' => "Aruba", 'PhoneCode' => 297),
            array('CountryId' => 13, 'CountryCode' => 'AU', 'CountryName' => "Australia", 'PhoneCode' => 61),
            array('CountryId' => 14, 'CountryCode' => 'AT', 'CountryName' => "Austria", 'PhoneCode' => 43),
            array('CountryId' => 15, 'CountryCode' => 'AZ', 'CountryName' => "Azerbaijan", 'PhoneCode' => 994),
            array('CountryId' => 16, 'CountryCode' => 'BS', 'CountryName' => "Bahamas The", 'PhoneCode' => 1242),
            array('CountryId' => 17, 'CountryCode' => 'BH', 'CountryName' => "Bahrain", 'PhoneCode' => 973),
            array('CountryId' => 18, 'CountryCode' => 'BD', 'CountryName' => "Bangladesh", 'PhoneCode' => 880),
            array('CountryId' => 19, 'CountryCode' => 'BB', 'CountryName' => "Barbados", 'PhoneCode' => 1246),
            array('CountryId' => 20, 'CountryCode' => 'BY', 'CountryName' => "Belarus", 'PhoneCode' => 375),
            array('CountryId' => 21, 'CountryCode' => 'BE', 'CountryName' => "Belgium", 'PhoneCode' => 32),
            array('CountryId' => 22, 'CountryCode' => 'BZ', 'CountryName' => "Belize", 'PhoneCode' => 501),
            array('CountryId' => 23, 'CountryCode' => 'BJ', 'CountryName' => "Benin", 'PhoneCode' => 229),
            array('CountryId' => 24, 'CountryCode' => 'BM', 'CountryName' => "Bermuda", 'PhoneCode' => 1441),
            array('CountryId' => 25, 'CountryCode' => 'BT', 'CountryName' => "Bhutan", 'PhoneCode' => 975),
            array('CountryId' => 26, 'CountryCode' => 'BO', 'CountryName' => "Bolivia", 'PhoneCode' => 591),
            array('CountryId' => 27, 'CountryCode' => 'BA', 'CountryName' => "Bosnia and Herzegovina", 'PhoneCode' => 387),
            array('CountryId' => 28, 'CountryCode' => 'BW', 'CountryName' => "Botswana", 'PhoneCode' => 267),
            array('CountryId' => 29, 'CountryCode' => 'BV', 'CountryName' => "Bouvet Island", 'PhoneCode' => 0),
            array('CountryId' => 30, 'CountryCode' => 'BR', 'CountryName' => "Brazil", 'PhoneCode' => 55),
            array('CountryId' => 31, 'CountryCode' => 'IO', 'CountryName' => "British Indian Ocean Territory", 'PhoneCode' => 246),
            array('CountryId' => 32, 'CountryCode' => 'BN', 'CountryName' => "Brunei", 'PhoneCode' => 673),
            array('CountryId' => 33, 'CountryCode' => 'BG', 'CountryName' => "Bulgaria", 'PhoneCode' => 359),
            array('CountryId' => 34, 'CountryCode' => 'BF', 'CountryName' => "Burkina Faso", 'PhoneCode' => 226),
            array('CountryId' => 35, 'CountryCode' => 'BI', 'CountryName' => "Burundi", 'PhoneCode' => 257),
            array('CountryId' => 36, 'CountryCode' => 'KH', 'CountryName' => "Cambodia", 'PhoneCode' => 855),
            array('CountryId' => 37, 'CountryCode' => 'CM', 'CountryName' => "Cameroon", 'PhoneCode' => 237),
            array('CountryId' => 38, 'CountryCode' => 'CA', 'CountryName' => "Canada", 'PhoneCode' => 1),
            array('CountryId' => 39, 'CountryCode' => 'CV', 'CountryName' => "Cape Verde", 'PhoneCode' => 238),
            array('CountryId' => 40, 'CountryCode' => 'KY', 'CountryName' => "Cayman Islands", 'PhoneCode' => 1345),
            array('CountryId' => 41, 'CountryCode' => 'CF', 'CountryName' => "Central African Republic", 'PhoneCode' => 236),
            array('CountryId' => 42, 'CountryCode' => 'TD', 'CountryName' => "Chad", 'PhoneCode' => 235),
            array('CountryId' => 43, 'CountryCode' => 'CL', 'CountryName' => "Chile", 'PhoneCode' => 56),
            array('CountryId' => 44, 'CountryCode' => 'CN', 'CountryName' => "China", 'PhoneCode' => 86),
            array('CountryId' => 45, 'CountryCode' => 'CX', 'CountryName' => "Christmas Island", 'PhoneCode' => 61),
            array('CountryId' => 46, 'CountryCode' => 'CC', 'CountryName' => "Cocos (Keeling) Islands", 'PhoneCode' => 672),
            array('CountryId' => 47, 'CountryCode' => 'CO', 'CountryName' => "Colombia", 'PhoneCode' => 57),
            array('CountryId' => 48, 'CountryCode' => 'KM', 'CountryName' => "Comoros", 'PhoneCode' => 269),
            array('CountryId' => 49, 'CountryCode' => 'CG', 'CountryName' => "Congo", 'PhoneCode' => 242),
            array('CountryId' => 50, 'CountryCode' => 'CD', 'CountryName' => "Congo The Democratic Republic Of The", 'PhoneCode' => 242),
            array('CountryId' => 51, 'CountryCode' => 'CK', 'CountryName' => "Cook Islands", 'PhoneCode' => 682),
            array('CountryId' => 52, 'CountryCode' => 'CR', 'CountryName' => "Costa Rica", 'PhoneCode' => 506),
            array('CountryId' => 53, 'CountryCode' => 'CI', 'CountryName' => "Cote D Ivoire (Ivory Coast)", 'PhoneCode' => 225),
            array('CountryId' => 54, 'CountryCode' => 'HR', 'CountryName' => "Croatia (Hrvatska)", 'PhoneCode' => 385),
            array('CountryId' => 55, 'CountryCode' => 'CU', 'CountryName' => "Cuba", 'PhoneCode' => 53),
            array('CountryId' => 56, 'CountryCode' => 'CY', 'CountryName' => "Cyprus", 'PhoneCode' => 357),
            array('CountryId' => 57, 'CountryCode' => 'CZ', 'CountryName' => "Czech Republic", 'PhoneCode' => 420),
            array('CountryId' => 58, 'CountryCode' => 'DK', 'CountryName' => "Denmark", 'PhoneCode' => 45),
            array('CountryId' => 59, 'CountryCode' => 'DJ', 'CountryName' => "Djibouti", 'PhoneCode' => 253),
            array('CountryId' => 60, 'CountryCode' => 'DM', 'CountryName' => "Dominica", 'PhoneCode' => 1767),
            array('CountryId' => 61, 'CountryCode' => 'DO', 'CountryName' => "Dominican Republic", 'PhoneCode' => 1809),
            array('CountryId' => 62, 'CountryCode' => 'TP', 'CountryName' => "East Timor", 'PhoneCode' => 670),
            array('CountryId' => 63, 'CountryCode' => 'EC', 'CountryName' => "Ecuador", 'PhoneCode' => 593),
            array('CountryId' => 64, 'CountryCode' => 'EG', 'CountryName' => "Egypt", 'PhoneCode' => 20),
            array('CountryId' => 65, 'CountryCode' => 'SV', 'CountryName' => "El Salvador", 'PhoneCode' => 503),
            array('CountryId' => 66, 'CountryCode' => 'GQ', 'CountryName' => "Equatorial Guinea", 'PhoneCode' => 240),
            array('CountryId' => 67, 'CountryCode' => 'ER', 'CountryName' => "Eritrea", 'PhoneCode' => 291),
            array('CountryId' => 68, 'CountryCode' => 'EE', 'CountryName' => "Estonia", 'PhoneCode' => 372),
            array('CountryId' => 69, 'CountryCode' => 'ET', 'CountryName' => "Ethiopia", 'PhoneCode' => 251),
            array('CountryId' => 70, 'CountryCode' => 'XA', 'CountryName' => "External Territories of Australia", 'PhoneCode' => 61),
            array('CountryId' => 71, 'CountryCode' => 'FK', 'CountryName' => "Falkland Islands", 'PhoneCode' => 500),
            array('CountryId' => 72, 'CountryCode' => 'FO', 'CountryName' => "Faroe Islands", 'PhoneCode' => 298),
            array('CountryId' => 73, 'CountryCode' => 'FJ', 'CountryName' => "Fiji Islands", 'PhoneCode' => 679),
            array('CountryId' => 74, 'CountryCode' => 'FI', 'CountryName' => "Finland", 'PhoneCode' => 358),
            array('CountryId' => 75, 'CountryCode' => 'FR', 'CountryName' => "France", 'PhoneCode' => 33),
            array('CountryId' => 76, 'CountryCode' => 'GF', 'CountryName' => "French Guiana", 'PhoneCode' => 594),
            array('CountryId' => 77, 'CountryCode' => 'PF', 'CountryName' => "French Polynesia", 'PhoneCode' => 689),
            array('CountryId' => 78, 'CountryCode' => 'TF', 'CountryName' => "French Southern Territories", 'PhoneCode' => 0),
            array('CountryId' => 79, 'CountryCode' => 'GA', 'CountryName' => "Gabon", 'PhoneCode' => 241),
            array('CountryId' => 80, 'CountryCode' => 'GM', 'CountryName' => "Gambia The", 'PhoneCode' => 220),
            array('CountryId' => 81, 'CountryCode' => 'GE', 'CountryName' => "Georgia", 'PhoneCode' => 995),
            array('CountryId' => 82, 'CountryCode' => 'DE', 'CountryName' => "Germany", 'PhoneCode' => 49),
            array('CountryId' => 83, 'CountryCode' => 'GH', 'CountryName' => "Ghana", 'PhoneCode' => 233),
            array('CountryId' => 84, 'CountryCode' => 'GI', 'CountryName' => "Gibraltar", 'PhoneCode' => 350),
            array('CountryId' => 85, 'CountryCode' => 'GR', 'CountryName' => "Greece", 'PhoneCode' => 30),
            array('CountryId' => 86, 'CountryCode' => 'GL', 'CountryName' => "Greenland", 'PhoneCode' => 299),
            array('CountryId' => 87, 'CountryCode' => 'GD', 'CountryName' => "Grenada", 'PhoneCode' => 1473),
            array('CountryId' => 88, 'CountryCode' => 'GP', 'CountryName' => "Guadeloupe", 'PhoneCode' => 590),
            array('CountryId' => 89, 'CountryCode' => 'GU', 'CountryName' => "Guam", 'PhoneCode' => 1671),
            array('CountryId' => 90, 'CountryCode' => 'GT', 'CountryName' => "Guatemala", 'PhoneCode' => 502),
            array('CountryId' => 91, 'CountryCode' => 'XU', 'CountryName' => "Guernsey and Alderney", 'PhoneCode' => 44),
            array('CountryId' => 92, 'CountryCode' => 'GN', 'CountryName' => "Guinea", 'PhoneCode' => 224),
            array('CountryId' => 93, 'CountryCode' => 'GW', 'CountryName' => "Guinea-Bissau", 'PhoneCode' => 245),
            array('CountryId' => 94, 'CountryCode' => 'GY', 'CountryName' => "Guyana", 'PhoneCode' => 592),
            array('CountryId' => 95, 'CountryCode' => 'HT', 'CountryName' => "Haiti", 'PhoneCode' => 509),
            array('CountryId' => 96, 'CountryCode' => 'HM', 'CountryName' => "Heard and McDonald Islands", 'PhoneCode' => 0),
            array('CountryId' => 97, 'CountryCode' => 'HN', 'CountryName' => "Honduras", 'PhoneCode' => 504),
            array('CountryId' => 98, 'CountryCode' => 'HK', 'CountryName' => "Hong Kong S.A.R.", 'PhoneCode' => 852),
            array('CountryId' => 99, 'CountryCode' => 'HU', 'CountryName' => "Hungary", 'PhoneCode' => 36),
            array('CountryId' => 100, 'CountryCode' => 'IS', 'CountryName' => "Iceland", 'PhoneCode' => 354),
            array('CountryId' => 101, 'CountryCode' => 'IN', 'CountryName' => "India", 'PhoneCode' => 91),
            array('CountryId' => 102, 'CountryCode' => 'CountryId', 'CountryName' => "Indonesia", 'PhoneCode' => 62),
            array('CountryId' => 103, 'CountryCode' => 'IR', 'CountryName' => "Iran", 'PhoneCode' => 98),
            array('CountryId' => 104, 'CountryCode' => 'IQ', 'CountryName' => "Iraq", 'PhoneCode' => 964),
            array('CountryId' => 105, 'CountryCode' => 'IE', 'CountryName' => "Ireland", 'PhoneCode' => 353),
            array('CountryId' => 106, 'CountryCode' => 'IL', 'CountryName' => "Israel", 'PhoneCode' => 972),
            array('CountryId' => 107, 'CountryCode' => 'IT', 'CountryName' => "Italy", 'PhoneCode' => 39),
            array('CountryId' => 108, 'CountryCode' => 'JM', 'CountryName' => "Jamaica", 'PhoneCode' => 1876),
            array('CountryId' => 109, 'CountryCode' => 'JP', 'CountryName' => "Japan", 'PhoneCode' => 81),
            array('CountryId' => 110, 'CountryCode' => 'XJ', 'CountryName' => "Jersey", 'PhoneCode' => 44),
            array('CountryId' => 111, 'CountryCode' => 'JO', 'CountryName' => "Jordan", 'PhoneCode' => 962),
            array('CountryId' => 112, 'CountryCode' => 'KZ', 'CountryName' => "Kazakhstan", 'PhoneCode' => 7),
            array('CountryId' => 113, 'CountryCode' => 'KE', 'CountryName' => "Kenya", 'PhoneCode' => 254),
            array('CountryId' => 114, 'CountryCode' => 'KI', 'CountryName' => "Kiribati", 'PhoneCode' => 686),
            array('CountryId' => 115, 'CountryCode' => 'KP', 'CountryName' => "Korea North", 'PhoneCode' => 850),
            array('CountryId' => 116, 'CountryCode' => 'KR', 'CountryName' => "Korea South", 'PhoneCode' => 82),
            array('CountryId' => 117, 'CountryCode' => 'KW', 'CountryName' => "Kuwait", 'PhoneCode' => 965),
            array('CountryId' => 118, 'CountryCode' => 'KG', 'CountryName' => "Kyrgyzstan", 'PhoneCode' => 996),
            array('CountryId' => 119, 'CountryCode' => 'LA', 'CountryName' => "Laos", 'PhoneCode' => 856),
            array('CountryId' => 120, 'CountryCode' => 'LV', 'CountryName' => "Latvia", 'PhoneCode' => 371),
            array('CountryId' => 121, 'CountryCode' => 'LB', 'CountryName' => "Lebanon", 'PhoneCode' => 961),
            array('CountryId' => 122, 'CountryCode' => 'LS', 'CountryName' => "Lesotho", 'PhoneCode' => 266),
            array('CountryId' => 123, 'CountryCode' => 'LR', 'CountryName' => "Liberia", 'PhoneCode' => 231),
            array('CountryId' => 124, 'CountryCode' => 'LY', 'CountryName' => "Libya", 'PhoneCode' => 218),
            array('CountryId' => 125, 'CountryCode' => 'LI', 'CountryName' => "Liechtenstein", 'PhoneCode' => 423),
            array('CountryId' => 126, 'CountryCode' => 'LT', 'CountryName' => "Lithuania", 'PhoneCode' => 370),
            array('CountryId' => 127, 'CountryCode' => 'LU', 'CountryName' => "Luxembourg", 'PhoneCode' => 352),
            array('CountryId' => 128, 'CountryCode' => 'MO', 'CountryName' => "Macau S.A.R.", 'PhoneCode' => 853),
            array('CountryId' => 129, 'CountryCode' => 'MK', 'CountryName' => "Macedonia", 'PhoneCode' => 389),
            array('CountryId' => 130, 'CountryCode' => 'MG', 'CountryName' => "Madagascar", 'PhoneCode' => 261),
            array('CountryId' => 131, 'CountryCode' => 'MW', 'CountryName' => "Malawi", 'PhoneCode' => 265),
            array('CountryId' => 132, 'CountryCode' => 'MY', 'CountryName' => "Malaysia", 'PhoneCode' => 60),
            array('CountryId' => 133, 'CountryCode' => 'MV', 'CountryName' => "Maldives", 'PhoneCode' => 960),
            array('CountryId' => 134, 'CountryCode' => 'ML', 'CountryName' => "Mali", 'PhoneCode' => 223),
            array('CountryId' => 135, 'CountryCode' => 'MT', 'CountryName' => "Malta", 'PhoneCode' => 356),
            array('CountryId' => 136, 'CountryCode' => 'XM', 'CountryName' => "Man (Isle of)", 'PhoneCode' => 44),
            array('CountryId' => 137, 'CountryCode' => 'MH', 'CountryName' => "Marshall Islands", 'PhoneCode' => 692),
            array('CountryId' => 138, 'CountryCode' => 'MQ', 'CountryName' => "Martinique", 'PhoneCode' => 596),
            array('CountryId' => 139, 'CountryCode' => 'MR', 'CountryName' => "Mauritania", 'PhoneCode' => 222),
            array('CountryId' => 140, 'CountryCode' => 'MU', 'CountryName' => "Mauritius", 'PhoneCode' => 230),
            array('CountryId' => 141, 'CountryCode' => 'YT', 'CountryName' => "Mayotte", 'PhoneCode' => 269),
            array('CountryId' => 142, 'CountryCode' => 'MX', 'CountryName' => "Mexico", 'PhoneCode' => 52),
            array('CountryId' => 143, 'CountryCode' => 'FM', 'CountryName' => "Micronesia", 'PhoneCode' => 691),
            array('CountryId' => 144, 'CountryCode' => 'MD', 'CountryName' => "Moldova", 'PhoneCode' => 373),
            array('CountryId' => 145, 'CountryCode' => 'MC', 'CountryName' => "Monaco", 'PhoneCode' => 377),
            array('CountryId' => 146, 'CountryCode' => 'MN', 'CountryName' => "Mongolia", 'PhoneCode' => 976),
            array('CountryId' => 147, 'CountryCode' => 'MS', 'CountryName' => "Montserrat", 'PhoneCode' => 1664),
            array('CountryId' => 148, 'CountryCode' => 'MA', 'CountryName' => "Morocco", 'PhoneCode' => 212),
            array('CountryId' => 149, 'CountryCode' => 'MZ', 'CountryName' => "Mozambique", 'PhoneCode' => 258),
            array('CountryId' => 150, 'CountryCode' => 'MM', 'CountryName' => "Myanmar", 'PhoneCode' => 95),
            array('CountryId' => 151, 'CountryCode' => 'NA', 'CountryName' => "Namibia", 'PhoneCode' => 264),
            array('CountryId' => 152, 'CountryCode' => 'NR', 'CountryName' => "Nauru", 'PhoneCode' => 674),
            array('CountryId' => 153, 'CountryCode' => 'NP', 'CountryName' => "Nepal", 'PhoneCode' => 977),
            array('CountryId' => 154, 'CountryCode' => 'AN', 'CountryName' => "Netherlands Antilles", 'PhoneCode' => 599),
            array('CountryId' => 155, 'CountryCode' => 'NL', 'CountryName' => "Netherlands The", 'PhoneCode' => 31),
            array('CountryId' => 156, 'CountryCode' => 'NC', 'CountryName' => "New Caledonia", 'PhoneCode' => 687),
            array('CountryId' => 157, 'CountryCode' => 'NZ', 'CountryName' => "New Zealand", 'PhoneCode' => 64),
            array('CountryId' => 158, 'CountryCode' => 'NI', 'CountryName' => "Nicaragua", 'PhoneCode' => 505),
            array('CountryId' => 159, 'CountryCode' => 'NE', 'CountryName' => "Niger", 'PhoneCode' => 227),
            array('CountryId' => 160, 'CountryCode' => 'NG', 'CountryName' => "Nigeria", 'PhoneCode' => 234),
            array('CountryId' => 161, 'CountryCode' => 'NU', 'CountryName' => "Niue", 'PhoneCode' => 683),
            array('CountryId' => 162, 'CountryCode' => 'NF', 'CountryName' => "Norfolk Island", 'PhoneCode' => 672),
            array('CountryId' => 163, 'CountryCode' => 'MP', 'CountryName' => "Northern Mariana Islands", 'PhoneCode' => 1670),
            array('CountryId' => 164, 'CountryCode' => 'NO', 'CountryName' => "Norway", 'PhoneCode' => 47),
            array('CountryId' => 165, 'CountryCode' => 'OM', 'CountryName' => "Oman", 'PhoneCode' => 968),
            array('CountryId' => 166, 'CountryCode' => 'PK', 'CountryName' => "Pakistan", 'PhoneCode' => 92),
            array('CountryId' => 167, 'CountryCode' => 'PW', 'CountryName' => "Palau", 'PhoneCode' => 680),
            array('CountryId' => 168, 'CountryCode' => 'PS', 'CountryName' => "Palestinian Territory Occupied", 'PhoneCode' => 970),
            array('CountryId' => 169, 'CountryCode' => 'PA', 'CountryName' => "Panama", 'PhoneCode' => 507),
            array('CountryId' => 170, 'CountryCode' => 'PG', 'CountryName' => "Papua new Guinea", 'PhoneCode' => 675),
            array('CountryId' => 171, 'CountryCode' => 'PY', 'CountryName' => "Paraguay", 'PhoneCode' => 595),
            array('CountryId' => 172, 'CountryCode' => 'PE', 'CountryName' => "Peru", 'PhoneCode' => 51),
            array('CountryId' => 173, 'CountryCode' => 'PH', 'CountryName' => "Philippines", 'PhoneCode' => 63),
            array('CountryId' => 174, 'CountryCode' => 'PN', 'CountryName' => "Pitcairn Island", 'PhoneCode' => 0),
            array('CountryId' => 175, 'CountryCode' => 'PL', 'CountryName' => "Poland", 'PhoneCode' => 48),
            array('CountryId' => 176, 'CountryCode' => 'PT', 'CountryName' => "Portugal", 'PhoneCode' => 351),
            array('CountryId' => 177, 'CountryCode' => 'PR', 'CountryName' => "Puerto Rico", 'PhoneCode' => 1787),
            array('CountryId' => 178, 'CountryCode' => 'QA', 'CountryName' => "Qatar", 'PhoneCode' => 974),
            array('CountryId' => 179, 'CountryCode' => 'RE', 'CountryName' => "Reunion", 'PhoneCode' => 262),
            array('CountryId' => 180, 'CountryCode' => 'RO', 'CountryName' => "Romania", 'PhoneCode' => 40),
            array('CountryId' => 181, 'CountryCode' => 'RU', 'CountryName' => "Russia", 'PhoneCode' => 70),
            array('CountryId' => 182, 'CountryCode' => 'RW', 'CountryName' => "Rwanda", 'PhoneCode' => 250),
            array('CountryId' => 183, 'CountryCode' => 'SH', 'CountryName' => "Saint Helena", 'PhoneCode' => 290),
            array('CountryId' => 184, 'CountryCode' => 'KN', 'CountryName' => "Saint Kitts And Nevis", 'PhoneCode' => 1869),
            array('CountryId' => 185, 'CountryCode' => 'LC', 'CountryName' => "Saint Lucia", 'PhoneCode' => 1758),
            array('CountryId' => 186, 'CountryCode' => 'PM', 'CountryName' => "Saint Pierre and Miquelon", 'PhoneCode' => 508),
            array('CountryId' => 187, 'CountryCode' => 'VC', 'CountryName' => "Saint Vincent And The Grenadines", 'PhoneCode' => 1784),
            array('CountryId' => 188, 'CountryCode' => 'WS', 'CountryName' => "Samoa", 'PhoneCode' => 684),
            array('CountryId' => 189, 'CountryCode' => 'SM', 'CountryName' => "San Marino", 'PhoneCode' => 378),
            array('CountryId' => 190, 'CountryCode' => 'ST', 'CountryName' => "Sao Tome and Principe", 'PhoneCode' => 239),
            array('CountryId' => 191, 'CountryCode' => 'SA', 'CountryName' => "Saudi Arabia", 'PhoneCode' => 966),
            array('CountryId' => 192, 'CountryCode' => 'SN', 'CountryName' => "Senegal", 'PhoneCode' => 221),
            array('CountryId' => 193, 'CountryCode' => 'RS', 'CountryName' => "Serbia", 'PhoneCode' => 381),
            array('CountryId' => 194, 'CountryCode' => 'SC', 'CountryName' => "Seychelles", 'PhoneCode' => 248),
            array('CountryId' => 195, 'CountryCode' => 'SL', 'CountryName' => "Sierra Leone", 'PhoneCode' => 232),
            array('CountryId' => 196, 'CountryCode' => 'SG', 'CountryName' => "Singapore", 'PhoneCode' => 65),
            array('CountryId' => 197, 'CountryCode' => 'SK', 'CountryName' => "Slovakia", 'PhoneCode' => 421),
            array('CountryId' => 198, 'CountryCode' => 'SI', 'CountryName' => "Slovenia", 'PhoneCode' => 386),
            array('CountryId' => 199, 'CountryCode' => 'XG', 'CountryName' => "Smaller Territories of the UK", 'PhoneCode' => 44),
            array('CountryId' => 200, 'CountryCode' => 'SB', 'CountryName' => "Solomon Islands", 'PhoneCode' => 677),
            array('CountryId' => 201, 'CountryCode' => 'SO', 'CountryName' => "Somalia", 'PhoneCode' => 252),
            array('CountryId' => 202, 'CountryCode' => 'ZA', 'CountryName' => "South Africa", 'PhoneCode' => 27),
            array('CountryId' => 203, 'CountryCode' => 'GS', 'CountryName' => "South Georgia", 'PhoneCode' => 0),
            array('CountryId' => 204, 'CountryCode' => 'SS', 'CountryName' => "South Sudan", 'PhoneCode' => 211),
            array('CountryId' => 205, 'CountryCode' => 'ES', 'CountryName' => "Spain", 'PhoneCode' => 34),
            array('CountryId' => 206, 'CountryCode' => 'LK', 'CountryName' => "Sri Lanka", 'PhoneCode' => 94),
            array('CountryId' => 207, 'CountryCode' => 'SD', 'CountryName' => "Sudan", 'PhoneCode' => 249),
            array('CountryId' => 208, 'CountryCode' => 'SR', 'CountryName' => "Suriname", 'PhoneCode' => 597),
            array('CountryId' => 209, 'CountryCode' => 'SJ', 'CountryName' => "Svalbard And Jan Mayen Islands", 'PhoneCode' => 47),
            array('CountryId' => 210, 'CountryCode' => 'SZ', 'CountryName' => "Swaziland", 'PhoneCode' => 268),
            array('CountryId' => 211, 'CountryCode' => 'SE', 'CountryName' => "Sweden", 'PhoneCode' => 46),
            array('CountryId' => 212, 'CountryCode' => 'CH', 'CountryName' => "Switzerland", 'PhoneCode' => 41),
            array('CountryId' => 213, 'CountryCode' => 'SY', 'CountryName' => "Syria", 'PhoneCode' => 963),
            array('CountryId' => 214, 'CountryCode' => 'TW', 'CountryName' => "Taiwan", 'PhoneCode' => 886),
            array('CountryId' => 215, 'CountryCode' => 'TJ', 'CountryName' => "Tajikistan", 'PhoneCode' => 992),
            array('CountryId' => 216, 'CountryCode' => 'TZ', 'CountryName' => "Tanzania", 'PhoneCode' => 255),
            array('CountryId' => 217, 'CountryCode' => 'TH', 'CountryName' => "Thailand", 'PhoneCode' => 66),
            array('CountryId' => 218, 'CountryCode' => 'TG', 'CountryName' => "Togo", 'PhoneCode' => 228),
            array('CountryId' => 219, 'CountryCode' => 'TK', 'CountryName' => "Tokelau", 'PhoneCode' => 690),
            array('CountryId' => 220, 'CountryCode' => 'TO', 'CountryName' => "Tonga", 'PhoneCode' => 676),
            array('CountryId' => 221, 'CountryCode' => 'TT', 'CountryName' => "Trinidad And Tobago", 'PhoneCode' => 1868),
            array('CountryId' => 222, 'CountryCode' => 'TN', 'CountryName' => "Tunisia", 'PhoneCode' => 216),
            array('CountryId' => 223, 'CountryCode' => 'TR', 'CountryName' => "Turkey", 'PhoneCode' => 90),
            array('CountryId' => 224, 'CountryCode' => 'TM', 'CountryName' => "Turkmenistan", 'PhoneCode' => 7370),
            array('CountryId' => 225, 'CountryCode' => 'TC', 'CountryName' => "Turks And Caicos Islands", 'PhoneCode' => 1649),
            array('CountryId' => 226, 'CountryCode' => 'TV', 'CountryName' => "Tuvalu", 'PhoneCode' => 688),
            array('CountryId' => 227, 'CountryCode' => 'UG', 'CountryName' => "Uganda", 'PhoneCode' => 256),
            array('CountryId' => 228, 'CountryCode' => 'UA', 'CountryName' => "Ukraine", 'PhoneCode' => 380),
            array('CountryId' => 229, 'CountryCode' => 'AE', 'CountryName' => "United Arab Emirates", 'PhoneCode' => 971),
            array('CountryId' => 230, 'CountryCode' => 'GB', 'CountryName' => "United Kingdom", 'PhoneCode' => 44),
            array('CountryId' => 231, 'CountryCode' => 'US', 'CountryName' => "United States", 'PhoneCode' => 1),
            array('CountryId' => 232, 'CountryCode' => 'UM', 'CountryName' => "United States Minor Outlying Islands", 'PhoneCode' => 1),
            array('CountryId' => 233, 'CountryCode' => 'UY', 'CountryName' => "Uruguay", 'PhoneCode' => 598),
            array('CountryId' => 234, 'CountryCode' => 'UZ', 'CountryName' => "Uzbekistan", 'PhoneCode' => 998),
            array('CountryId' => 235, 'CountryCode' => 'VU', 'CountryName' => "Vanuatu", 'PhoneCode' => 678),
            array('CountryId' => 236, 'CountryCode' => 'VA', 'CountryName' => "Vatican City State (Holy See)", 'PhoneCode' => 39),
            array('CountryId' => 237, 'CountryCode' => 'VE', 'CountryName' => "Venezuela", 'PhoneCode' => 58),
            array('CountryId' => 238, 'CountryCode' => 'VN', 'CountryName' => "Vietnam", 'PhoneCode' => 84),
            array('CountryId' => 239, 'CountryCode' => 'VG', 'CountryName' => "Virgin Islands (British)", 'PhoneCode' => 1284),
            array('CountryId' => 240, 'CountryCode' => 'VI', 'CountryName' => "Virgin Islands (US)", 'PhoneCode' => 1340),
            array('CountryId' => 241, 'CountryCode' => 'WF', 'CountryName' => "Wallis And Futuna Islands", 'PhoneCode' => 681),
            array('CountryId' => 242, 'CountryCode' => 'EH', 'CountryName' => "Western Sahara", 'PhoneCode' => 212),
            array('CountryId' => 243, 'CountryCode' => 'YE', 'CountryName' => "Yemen", 'PhoneCode' => 967),
            array('CountryId' => 244, 'CountryCode' => 'YU', 'CountryName' => "Yugoslavia", 'PhoneCode' => 38),
            array('CountryId' => 245, 'CountryCode' => 'ZM', 'CountryName' => "Zambia", 'PhoneCode' => 260),
            array('CountryId' => 246, 'CountryCode' => 'ZW', 'CountryName' => "Zimbabwe", 'PhoneCode' => 263),
        );
        DB::table('Country')->insert($Countries);
    }
}
