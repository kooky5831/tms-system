<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TrainerQualifications;

class TrainerQualificationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $qualifications = [
        	0 => ["code" => "0", "title" => "NO FORMAL QUALIFICATION / PRE-PRIMARY / LOWER PRIMARY"],
			1 => ["code" => "01", "title" => "Never attended school"],
			2 => ["code" => "02", "title" => "Pre-Primary (i.e. Nursery, Kindergarten 1, Kindergarten 2)"],
			3 => ["code" => "03", "title" => "Primary education without Primary School Leaving Examination (PSLE) / Primary School Proficiency Examination (PSPE) certificate or equivalent"],
			4 => ["code" => "04", "title" => "Certificate in BEST 1-3"],
			5 => ["code" => "1", "title" => "PRIMARY"],
			6 => ["code" => "11", "title" => "Primary School Leaving Examination (PSLE) / Primary School Proficiency Examination (PSPE) certificate or equivalent"],
			7 => ["code" => "12", "title" => "Certificate in BEST 4"],
			8 => ["code" => "13", "title" => "At least 3 achievements for different Workplace Literacy or Numeracy (WPLN) skills at Level 1 or 2"],
			9 => ["code" => "2", "title" => "LOWER SECONDARY"],
			10 => ["code" => "21", "title" => "Secondary education without any subject pass at GCE 'O'/'N' Level or equivalent"],
			11 => ["code" => "22", "title" => "Certificate in WISE 1-3"],
			12 => ["code" => "23", "title" => "Basic vocational certificate (including ITE Basic Vocational Training)"],
			13 => ["code" => "24", "title" => "At least 3 achievements for different Workplace Literacy or Numeracy (WPLN) skills at Level 3 or 4"],
			14 => ["code" => "3", "title" => "SECONDARY"],
			15 => ["code" => "31", "title" => "At least 1 subject pass at GCE 'N' Level"],
			16 => ["code" => "32", "title" => "At least 1 subject pass at GCE 'O' Level"],
			17 => ["code" => "33", "title" => "National ITE Certificate (Intermediate) or equivalent (including National Technical Certificate (NTC) Grade 3, Certificate of Vocational Training, BCA Builder Certificate)"],
			18 => ["code" => "34", "title" => "ITE Skills Certificate (ISC) or equivalent (including Certificate of Competency, Certificate in Service Skills)"],
			19 => ["code" => "35", "title" => "At least 3 achievements for different Workplace Literacy or Numeracy (WPLN) skills at Level 5 and above"],
			20 => ["code" => "39", "title" => "Other secondary education/certificates or equivalent"],
			21 => ["code" => "4", "title" => "POST-SECONDARY (NON-TERTIARY): GENERAL AND VOCATIONAL"],
			22 => ["code" => "41", "title" => "At least 1 subject pass at GCE 'A'/'H2' Level or equivalent (general)"],
			23 => ["code" => "42", "title" => "National ITE Certificate (Nitec) or equivalent (including Post Nitec Certificate, Specialist Nitec, Certificate in Office Skills, National Technical Certificate (NTC) Grade 2, National Certificate in Nursing, BCA Advanced Builder Certificate)"],
			24 => ["code" => "43", "title" => "Higher Nitec or equivalent (including Certificate in Business Skills, Industrial Technician Certificate)"],
			25 => ["code" => "44", "title" => "Master Nitec or equivalent (including NTC Grade 1)"],
			26 => ["code" => "45", "title" => "WSQ Certificate or equivalent"],
			27 => ["code" => "46", "title" => "WSQ Higher Certificate or equivalent"],
			28 => ["code" => "47", "title" => "WSQ Advanced Certificate or equivalent"],
			29 => ["code" => "48", "title" => "Other post-secondary (non-tertiary; general) qualifications or equivalent (including International Baccalaureate / NUS High School Diploma)"],
			30 => ["code" => "49", "title" => "Other post-secondary (non-tertiary; vocational) certificates/qualifications or equivalent (including SIM certificate)"],
			31 => ["code" => "5", "title" => "POLYTECHNIC DIPLOMA"],
			32 => ["code" => "51", "title" => "Polytechnic diploma"],
			33 => ["code" => "52", "title" => "Polytechnic post-diploma (including polytechnic advanced/specialist/management/graduate diploma, diploma (conversion))"],
			34 => ["code" => "6", "title" => "PROFESSIONAL QUALIFICATION AND OTHER DIPLOMA"],
			35 => ["code" => "61", "title" => "ITE diploma"],
			36 => ["code" => "62", "title" => "Other locally or externally developed diploma (including NIE diploma, SIM diploma, LASALLE diploma, NAFA diploma)"],
			37 => ["code" => "63", "title" => "Qualification awarded by professional bodies (including ACCA, CFA)"],
			38 => ["code" => "64", "title" => "WSQ diploma"],
			39 => ["code" => "65", "title" => "WSQ specialist diploma"],
			40 => ["code" => "69", "title" => "Other post-diploma qualifications or equivalent"],
			41 => ["code" => "7", "title" => "BACHELOR'S OR EQUIVALENT"],
			42 => ["code" => "71", "title" => "First degree or equivalent"],
			43 => ["code" => "72", "title" => "Long first degree or equivalent"],
			44 => ["code" => "8", "title" => "POSTGRADUATE DIPLOMA/CERTIFICATE (EXCLUDING MASTER'S AND DOCTORATE)"],
			45 => ["code" => "81", "title" => "Postgraduate diploma/certificate (including NIE postgraduate diploma)"],
			46 => ["code" => "82", "title" => "WSQ graduate certificate"],
			47 => ["code" => "83", "title" => "WSQ graduate diploma"],
			48 => ["code" => "9", "title" => "MASTER'S AND DOCTORATE OR EQUIVALENT"],
			49 => ["code" => "91", "title" => "Master's degree or equivalent"],
			50 => ["code" => "92", "title" => "Doctoral degree or equivalent"],
			51 => ["code" => "N", "title" => "MODULAR CERTIFICATION (NON-AWARD COURSES / NON-FULL QUALIFICATIONS)"],
			52 => ["code" => "N1", "title" => "At least 1 WSQ Statement of Attainment or ITE modular certificate at post-secondary level (non-tertiary) or equivalent"],
			53 => ["code" => "N2", "title" => "At least 1 WSQ Statement of Attainment or other modular certificate at diploma level or equivalent (including polytechnic post-diploma certificate)"],
			54 => ["code" => "N3", "title" => "At least 1 WSQ Statement of Attainment or other modular certificate at degree level or equivalent"],
			55 => ["code" => "N4", "title" => "At least 1 WSQ Statement of Attainment or other modular certificate at postgraduate level or equivalent"],
			56 => ["code" => "N9", "title" => "Other statements of attainment, modular certificates or equivalent"],
			57 => ["code" => "X", "title" => "NOT REPORTED"],
			58 => ["code" => "XX", "title" => "Not reported"],
        ];
        foreach ($qualifications as $k => $qualification) {
            $record = new TrainerQualifications;
            $record->code                   = $qualification['code'];
            $record->title                  = $qualification['title'];
            $record->created_by             = 1;
            $record->updated_by             = 1;
            $record->save();
        }
    }
}
