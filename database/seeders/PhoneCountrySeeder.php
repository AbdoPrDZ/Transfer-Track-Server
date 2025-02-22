<?php

namespace Database\Seeders;

use App\Models\PhoneCountry;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class PhoneCountrySeeder extends Seeder {

  /**
   * Run the database seeds.
   */
  public function run(): void {
    $json = <<<EOF
    [
      { "code": "AD", "name": "Andorra", "call_code": "+376" },
      { "code": "AE", "name": "United Arab Emirates", "call_code": "+971" },
      { "code": "AF", "name": "Afghanistan", "call_code": "+93" },
      { "code": "AG", "name": "Antigua and Barbuda", "call_code": "+1268" },
      { "code": "AI", "name": "Anguilla", "call_code": "+1264" },
      { "code": "AL", "name": "Albania", "call_code": "+355" },
      { "code": "AM", "name": "Armenia", "call_code": "+374" },
      { "code": "AO", "name": "Angola", "call_code": "+244" },
      { "code": "AQ", "name": "Antarctica", "call_code": "N/A" },
      { "code": "AR", "name": "Argentina", "call_code": "+54" },
      { "code": "AS", "name": "American Samoa", "call_code": "+1684" },
      { "code": "AT", "name": "Austria", "call_code": "+43" },
      { "code": "AU", "name": "Australia", "call_code": "+61" },
      { "code": "AW", "name": "Aruba", "call_code": "+297" },
      { "code": "AX", "name": "\u00c5land Islands", "call_code": "+35818" },
      { "code": "AZ", "name": "Azerbaijan", "call_code": "+994" },
      { "code": "BA", "name": "Bosnia and Herzegovina", "call_code": "+387" },
      { "code": "BB", "name": "Barbados", "call_code": "+1246" },
      { "code": "BD", "name": "Bangladesh", "call_code": "+880" },
      { "code": "BE", "name": "Belgium", "call_code": "+32" },
      { "code": "BF", "name": "Burkina Faso", "call_code": "+226" },
      { "code": "BG", "name": "Bulgaria", "call_code": "+359" },
      { "code": "BH", "name": "Bahrain", "call_code": "+973" },
      { "code": "BI", "name": "Burundi", "call_code": "+257" },
      { "code": "BJ", "name": "Benin", "call_code": "+229" },
      { "code": "BL", "name": "Saint Barth\u00e9lemy", "call_code": "+590" },
      { "code": "BM", "name": "Bermuda", "call_code": "+1441" },
      { "code": "BN", "name": "Brunei", "call_code": "+673" },
      { "code": "BO", "name": "Bolivia", "call_code": "+591" },
      { "code": "BQ", "name": "Caribbean Netherlands", "call_code": "+599" },
      { "code": "BR", "name": "Brazil", "call_code": "+55" },
      { "code": "BS", "name": "Bahamas", "call_code": "+1242" },
      { "code": "BT", "name": "Bhutan", "call_code": "+975" },
      { "code": "BV", "name": "Bouvet Island", "call_code": "+47" },
      { "code": "BW", "name": "Botswana", "call_code": "+267" },
      { "code": "BY", "name": "Belarus", "call_code": "+375" },
      { "code": "BZ", "name": "Belize", "call_code": "+501" },
      { "code": "CA", "name": "Canada", "call_code": "+1" },
      { "code": "CC", "name": "Cocos (Keeling) Islands", "call_code": "+61" },
      { "code": "CD", "name": "DR Congo", "call_code": "+243" },
      { "code": "CF", "name": "Central African Republic", "call_code": "+236" },
      { "code": "CG", "name": "Republic of the Congo", "call_code": "+242" },
      { "code": "CH", "name": "Switzerland", "call_code": "+41" },
      { "code": "CI", "name": "Ivory Coast", "call_code": "+225" },
      { "code": "CK", "name": "Cook Islands", "call_code": "+682" },
      { "code": "CL", "name": "Chile", "call_code": "+56" },
      { "code": "CM", "name": "Cameroon", "call_code": "+237" },
      { "code": "CN", "name": "China", "call_code": "+86" },
      { "code": "CO", "name": "Colombia", "call_code": "+57" },
      { "code": "CR", "name": "Costa Rica", "call_code": "+506" },
      { "code": "CU", "name": "Cuba", "call_code": "+53" },
      { "code": "CV", "name": "Cape Verde", "call_code": "+238" },
      { "code": "CW", "name": "Cura\u00e7ao", "call_code": "+599" },
      { "code": "CX", "name": "Christmas Island", "call_code": "+61" },
      { "code": "CY", "name": "Cyprus", "call_code": "+357" },
      { "code": "CZ", "name": "Czechia", "call_code": "+420" },
      { "code": "DE", "name": "Germany", "call_code": "+49" },
      { "code": "DJ", "name": "Djibouti", "call_code": "+253" },
      { "code": "DK", "name": "Denmark", "call_code": "+45" },
      { "code": "DM", "name": "Dominica", "call_code": "+1767" },
      { "code": "DO", "name": "Dominican Republic", "call_code": "+1809" },
      { "code": "DZ", "name": "Algeria", "call_code": "+213" },
      { "code": "EC", "name": "Ecuador", "call_code": "+593" },
      { "code": "EE", "name": "Estonia", "call_code": "+372" },
      { "code": "EG", "name": "Egypt", "call_code": "+20" },
      { "code": "EH", "name": "Western Sahara", "call_code": "+2125288" },
      { "code": "ER", "name": "Eritrea", "call_code": "+291" },
      { "code": "ES", "name": "Spain", "call_code": "+34" },
      { "code": "ET", "name": "Ethiopia", "call_code": "+251" },
      { "code": "FI", "name": "Finland", "call_code": "+358" },
      { "code": "FJ", "name": "Fiji", "call_code": "+679" },
      { "code": "FK", "name": "Falkland Islands", "call_code": "+500" },
      { "code": "FM", "name": "Micronesia", "call_code": "+691" },
      { "code": "FO", "name": "Faroe Islands", "call_code": "+298" },
      { "code": "FR", "name": "France", "call_code": "+33" },
      { "code": "GA", "name": "Gabon", "call_code": "+241" },
      { "code": "GB", "name": "United Kingdom", "call_code": "+44" },
      { "code": "GD", "name": "Grenada", "call_code": "+1473" },
      { "code": "GE", "name": "Georgia", "call_code": "+995" },
      { "code": "GF", "name": "French Guiana", "call_code": "+594" },
      { "code": "GG", "name": "Guernsey", "call_code": "+44" },
      { "code": "GH", "name": "Ghana", "call_code": "+233" },
      { "code": "GI", "name": "Gibraltar", "call_code": "+350" },
      { "code": "GL", "name": "Greenland", "call_code": "+299" },
      { "code": "GM", "name": "Gambia", "call_code": "+220" },
      { "code": "GN", "name": "Guinea", "call_code": "+224" },
      { "code": "GP", "name": "Guadeloupe", "call_code": "+590" },
      { "code": "GQ", "name": "Equatorial Guinea", "call_code": "+240" },
      { "code": "GR", "name": "Greece", "call_code": "+30" },
      { "code": "GS", "name": "South Georgia", "call_code": "+500" },
      { "code": "GT", "name": "Guatemala", "call_code": "+502" },
      { "code": "GU", "name": "Guam", "call_code": "+1671" },
      { "code": "GW", "name": "Guinea-Bissau", "call_code": "+245" },
      { "code": "GY", "name": "Guyana", "call_code": "+592" },
      { "code": "HK", "name": "Hong Kong", "call_code": "+852" },
      { "code": "HM", "name": "Heard Island and McDonald Islands", "call_code": "N/A" },
      { "code": "HN", "name": "Honduras", "call_code": "+504" },
      { "code": "HR", "name": "Croatia", "call_code": "+385" },
      { "code": "HT", "name": "Haiti", "call_code": "+509" },
      { "code": "HU", "name": "Hungary", "call_code": "+36" },
      { "code": "ID", "name": "Indonesia", "call_code": "+62" },
      { "code": "IE", "name": "Ireland", "call_code": "+353" },
      { "code": "IM", "name": "Isle of Man", "call_code": "+44" },
      { "code": "IN", "name": "India", "call_code": "+91" },
      { "code": "IO", "name": "British Indian Ocean Territory", "call_code": "+246" },
      { "code": "IQ", "name": "Iraq", "call_code": "+964" },
      { "code": "IR", "name": "Iran", "call_code": "+98" },
      { "code": "IS", "name": "Iceland", "call_code": "+354" },
      { "code": "IT", "name": "Italy", "call_code": "+39" },
      { "code": "JE", "name": "Jersey", "call_code": "+44" },
      { "code": "JM", "name": "Jamaica", "call_code": "+1876" },
      { "code": "JO", "name": "Jordan", "call_code": "+962" },
      { "code": "JP", "name": "Japan", "call_code": "+81" },
      { "code": "KE", "name": "Kenya", "call_code": "+254" },
      { "code": "KG", "name": "Kyrgyzstan", "call_code": "+996" },
      { "code": "KH", "name": "Cambodia", "call_code": "+855" },
      { "code": "KI", "name": "Kiribati", "call_code": "+686" },
      { "code": "KM", "name": "Comoros", "call_code": "+269" },
      { "code": "KN", "name": "Saint Kitts and Nevis", "call_code": "+1869" },
      { "code": "KP", "name": "North Korea", "call_code": "+850" },
      { "code": "KR", "name": "South Korea", "call_code": "+82" },
      { "code": "KW", "name": "Kuwait", "call_code": "+965" },
      { "code": "KY", "name": "Cayman Islands", "call_code": "+1345" },
      { "code": "KZ", "name": "Kazakhstan", "call_code": "+76" },
      { "code": "LA", "name": "Laos", "call_code": "+856" },
      { "code": "LB", "name": "Lebanon", "call_code": "+961" },
      { "code": "LC", "name": "Saint Lucia", "call_code": "+1758" },
      { "code": "LI", "name": "Liechtenstein", "call_code": "+423" },
      { "code": "LK", "name": "Sri Lanka", "call_code": "+94" },
      { "code": "LR", "name": "Liberia", "call_code": "+231" },
      { "code": "LS", "name": "Lesotho", "call_code": "+266" },
      { "code": "LT", "name": "Lithuania", "call_code": "+370" },
      { "code": "LU", "name": "Luxembourg", "call_code": "+352" },
      { "code": "LV", "name": "Latvia", "call_code": "+371" },
      { "code": "LY", "name": "Libya", "call_code": "+218" },
      { "code": "MA", "name": "Morocco", "call_code": "+212" },
      { "code": "MC", "name": "Monaco", "call_code": "+377" },
      { "code": "MD", "name": "Moldova", "call_code": "+373" },
      { "code": "ME", "name": "Montenegro", "call_code": "+382" },
      { "code": "MF", "name": "Saint Martin", "call_code": "+590" },
      { "code": "MG", "name": "Madagascar", "call_code": "+261" },
      { "code": "MH", "name": "Marshall Islands", "call_code": "+692" },
      { "code": "MK", "name": "North Macedonia", "call_code": "+389" },
      { "code": "ML", "name": "Mali", "call_code": "+223" },
      { "code": "MM", "name": "Myanmar", "call_code": "+95" },
      { "code": "MN", "name": "Mongolia", "call_code": "+976" },
      { "code": "MO", "name": "Macau", "call_code": "+853" },
      { "code": "MP", "name": "Northern Mariana Islands", "call_code": "+1670" },
      { "code": "MQ", "name": "Martinique", "call_code": "+596" },
      { "code": "MR", "name": "Mauritania", "call_code": "+222" },
      { "code": "MS", "name": "Montserrat", "call_code": "+1664" },
      { "code": "MT", "name": "Malta", "call_code": "+356" },
      { "code": "MU", "name": "Mauritius", "call_code": "+230" },
      { "code": "MV", "name": "Maldives", "call_code": "+960" },
      { "code": "MW", "name": "Malawi", "call_code": "+265" },
      { "code": "MX", "name": "Mexico", "call_code": "+52" },
      { "code": "MY", "name": "Malaysia", "call_code": "+60" },
      { "code": "MZ", "name": "Mozambique", "call_code": "+258" },
      { "code": "NA", "name": "Namibia", "call_code": "+264" },
      { "code": "NC", "name": "New Caledonia", "call_code": "+687" },
      { "code": "NE", "name": "Niger", "call_code": "+227" },
      { "code": "NF", "name": "Norfolk Island", "call_code": "+672" },
      { "code": "NG", "name": "Nigeria", "call_code": "+234" },
      { "code": "NI", "name": "Nicaragua", "call_code": "+505" },
      { "code": "NL", "name": "Netherlands", "call_code": "+31" },
      { "code": "NO", "name": "Norway", "call_code": "+47" },
      { "code": "NP", "name": "Nepal", "call_code": "+977" },
      { "code": "NR", "name": "Nauru", "call_code": "+674" },
      { "code": "NU", "name": "Niue", "call_code": "+683" },
      { "code": "NZ", "name": "New Zealand", "call_code": "+64" },
      { "code": "OM", "name": "Oman", "call_code": "+968" },
      { "code": "PA", "name": "Panama", "call_code": "+507" },
      { "code": "PE", "name": "Peru", "call_code": "+51" },
      { "code": "PF", "name": "French Polynesia", "call_code": "+689" },
      { "code": "PG", "name": "Papua New Guinea", "call_code": "+675" },
      { "code": "PH", "name": "Philippines", "call_code": "+63" },
      { "code": "PK", "name": "Pakistan", "call_code": "+92" },
      { "code": "PL", "name": "Poland", "call_code": "+48" },
      { "code": "PM", "name": "Saint Pierre and Miquelon", "call_code": "+508" },
      { "code": "PN", "name": "Pitcairn Islands", "call_code": "+64" },
      { "code": "PR", "name": "Puerto Rico", "call_code": "+1787" },
      { "code": "PS", "name": "Palestine", "call_code": "+970" },
      { "code": "PT", "name": "Portugal", "call_code": "+351" },
      { "code": "PW", "name": "Palau", "call_code": "+680" },
      { "code": "PY", "name": "Paraguay", "call_code": "+595" },
      { "code": "QA", "name": "Qatar", "call_code": "+974" },
      { "code": "RE", "name": "R\u00e9union", "call_code": "+262" },
      { "code": "RO", "name": "Romania", "call_code": "+40" },
      { "code": "RS", "name": "Serbia", "call_code": "+381" },
      { "code": "RU", "name": "Russia", "call_code": "+73" },
      { "code": "RW", "name": "Rwanda", "call_code": "+250" },
      { "code": "SA", "name": "Saudi Arabia", "call_code": "+966" },
      { "code": "SB", "name": "Solomon Islands", "call_code": "+677" },
      { "code": "SC", "name": "Seychelles", "call_code": "+248" },
      { "code": "SD", "name": "Sudan", "call_code": "+249" },
      { "code": "SE", "name": "Sweden", "call_code": "+46" },
      { "code": "SG", "name": "Singapore", "call_code": "+65" },
      { "code": "SH", "name": "Saint Helena, Ascension and Tristan da Cunha", "call_code": "+290" },
      { "code": "SI", "name": "Slovenia", "call_code": "+386" },
      { "code": "SJ", "name": "Svalbard and Jan Mayen", "call_code": "+4779" },
      { "code": "SK", "name": "Slovakia", "call_code": "+421" },
      { "code": "SL", "name": "Sierra Leone", "call_code": "+232" },
      { "code": "SM", "name": "San Marino", "call_code": "+378" },
      { "code": "SN", "name": "Senegal", "call_code": "+221" },
      { "code": "SO", "name": "Somalia", "call_code": "+252" },
      { "code": "SR", "name": "Suriname", "call_code": "+597" },
      { "code": "SS", "name": "South Sudan", "call_code": "+211" },
      { "code": "ST", "name": "S\u00e3o Tom\u00e9 and Pr\u00edncipe", "call_code": "+239" },
      { "code": "SV", "name": "El Salvador", "call_code": "+503" },
      { "code": "SX", "name": "Sint Maarten", "call_code": "+1721" },
      { "code": "SY", "name": "Syria", "call_code": "+963" },
      { "code": "SZ", "name": "Eswatini", "call_code": "+268" },
      { "code": "TC", "name": "Turks and Caicos Islands", "call_code": "+1649" },
      { "code": "TD", "name": "Chad", "call_code": "+235" },
      { "code": "TF", "name": "French Southern and Antarctic Lands", "call_code": "+262" },
      { "code": "TG", "name": "Togo", "call_code": "+228" },
      { "code": "TH", "name": "Thailand", "call_code": "+66" },
      { "code": "TJ", "name": "Tajikistan", "call_code": "+992" },
      { "code": "TK", "name": "Tokelau", "call_code": "+690" },
      { "code": "TL", "name": "Timor-Leste", "call_code": "+670" },
      { "code": "TM", "name": "Turkmenistan", "call_code": "+993" },
      { "code": "TN", "name": "Tunisia", "call_code": "+216" },
      { "code": "TO", "name": "Tonga", "call_code": "+676" },
      { "code": "TR", "name": "Turkey", "call_code": "+90" },
      { "code": "TT", "name": "Trinidad and Tobago", "call_code": "+1868" },
      { "code": "TV", "name": "Tuvalu", "call_code": "+688" },
      { "code": "TW", "name": "Taiwan", "call_code": "+886" },
      { "code": "TZ", "name": "Tanzania", "call_code": "+255" },
      { "code": "UA", "name": "Ukraine", "call_code": "+380" },
      { "code": "UG", "name": "Uganda", "call_code": "+256" },
      { "code": "UM", "name": "United States Minor Outlying Islands", "call_code": "+268" },
      { "code": "US", "name": "United States", "call_code": "+1201" },
      { "code": "UY", "name": "Uruguay", "call_code": "+598" },
      { "code": "UZ", "name": "Uzbekistan", "call_code": "+998" },
      { "code": "VA", "name": "Vatican City", "call_code": "+3906698" },
      { "code": "VC", "name": "Saint Vincent and the Grenadines", "call_code": "+1784" },
      { "code": "VE", "name": "Venezuela", "call_code": "+58" },
      { "code": "VG", "name": "British Virgin Islands", "call_code": "+1284" },
      { "code": "VI", "name": "United States Virgin Islands", "call_code": "+1340" },
      { "code": "VN", "name": "Vietnam", "call_code": "+84" },
      { "code": "VU", "name": "Vanuatu", "call_code": "+678" },
      { "code": "WF", "name": "Wallis and Futuna", "call_code": "+681" },
      { "code": "WS", "name": "Samoa", "call_code": "+685" },
      { "code": "XK", "name": "Kosovo", "call_code": "+383" },
      { "code": "YE", "name": "Yemen", "call_code": "+967" },
      { "code": "YT", "name": "Mayotte", "call_code": "+262" },
      { "code": "ZA", "name": "South Africa", "call_code": "+27" },
      { "code": "ZM", "name": "Zambia", "call_code": "+260" },
      { "code": "ZW", "name": "Zimbabwe", "call_code": "+263" }
    ]
    EOF;

    $countries = json_decode($json, true);
    foreach ($countries as $country) {
      PhoneCountry::firstOrCreate([
        'code' => $country['code'],
        'name' => $country['name'],
        'call_code' => $country['call_code'],
      ]);
    }
  }

}
