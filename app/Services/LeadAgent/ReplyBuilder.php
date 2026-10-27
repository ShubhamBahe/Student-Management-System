<?php
namespace App\Services\LeadAgent;

/**
 * Deterministic, multilingual (Hindi / Hinglish / English) replies built only from database facts.
 * Used directly in "rule based" mode and as the fact-plan for the AI in "AI" mode.
 */
class ReplyBuilder {
    private const T = [
        // ---- fixed messages -------------------------------------------------
        'greeting' => [
            'en' => "Hello! I'm the %s parent helpline assistant. I can share %s's attendance, test marks, strongest and weakest subject, fees and teacher remarks, or give a full report. What would you like to know?",
            'hinglish' => "Namaste! Main %s ka parent helpline assistant hoon. Main %s ki attendance, test marks, sabse mazboot aur sabse kamzor subject, fees aur teacher remarks bata sakta hoon, ya poori report de sakta hoon. Aap kya jaanna chahte hain?",
            'hi' => "नमस्ते! मैं %s का पैरेंट हेल्पलाइन असिस्टेंट हूँ। मैं %s की उपस्थिति, टेस्ट के अंक, सबसे मजबूत और सबसे कमजोर विषय, फीस और शिक्षकों की टिप्पणियाँ बता सकता हूँ, या पूरी रिपोर्ट दे सकता हूँ। आप क्या जानना चाहते हैं?",
        ],
        'help' => [
            'en' => "I can help with attendance, test marks, strongest and weakest subject, fees, teacher remarks, or a full report for %s. What would you like to know?",
            'hinglish' => "Main %s ki attendance, test marks, sabse mazboot aur kamzor subject, fees, teacher remarks ya poori report bata sakta hoon. Aap kya jaanna chahte hain?",
            'hi' => "मैं %s की उपस्थिति, टेस्ट के अंक, सबसे मजबूत और कमजोर विषय, फीस, शिक्षकों की टिप्पणियाँ या पूरी रिपोर्ट बता सकता हूँ। आप क्या जानना चाहते हैं?",
        ],
        'ask_child' => [
            'en' => "Which child do you mean? Please reply with the number or the name:\n%s\nYou can also say \"both\".",
            'hinglish' => "Aap kis bachche ke baare mein poochh rahe hain? Number ya naam bhejiye:\n%s\nYa \"dono\" likhiye.",
            'hi' => "आप किस बच्चे के बारे में पूछ रहे हैं? नंबर या नाम भेजिए:\n%s\nया \"दोनों\" लिखिए।",
        ],
        'blocked' => [
            'en' => "Too many unsuccessful attempts. This number is blocked for about %d more minutes. Please try again later or contact the school office.",
            'hinglish' => "Bahut zyada galat koshishein ho gayi hain. Yeh number lagbhag %d minute ke liye block hai. Kripya baad mein try karein ya school office se sampark karein.",
            'hi' => "बहुत अधिक असफल प्रयास हो चुके हैं। यह नंबर लगभग %d मिनट के लिए ब्लॉक है। कृपया बाद में कोशिश करें या स्कूल ऑफिस से संपर्क करें।",
        ],
        'blocked_now' => [
            'en' => "Too many unsuccessful attempts. For security this number is now blocked for %d minutes. Please contact the school office if you need help.",
            'hinglish' => "Bahut zyada galat koshishein ho gayi hain. Suraksha ke liye yeh number ab %d minute ke liye block hai. Madad ke liye school office se sampark karein.",
            'hi' => "बहुत अधिक असफल प्रयास हो चुके हैं। सुरक्षा के लिए यह नंबर अब %d मिनट के लिए ब्लॉक है। मदद के लिए स्कूल ऑफिस से संपर्क करें।",
        ],
        'not_registered' => [
            'en' => "Sorry, this phone number is not registered with the school, so I cannot share any details. Please send your child's student ID, or contact the school office to register your number.",
            'hinglish' => "Maaf kijiye, yeh phone number school mein registered nahi hai, isliye main koi jaankari nahi de sakta. Apne bachche ka student ID bhejiye, ya number register karane ke liye school office se sampark karein.",
            'hi' => "क्षमा करें, यह फोन नंबर स्कूल में पंजीकृत नहीं है, इसलिए मैं कोई जानकारी नहीं दे सकता। कृपया अपने बच्चे का स्टूडेंट आईडी भेजें, या नंबर पंजीकृत कराने के लिए स्कूल ऑफिस से संपर्क करें।",
        ],
        'id_not_found' => [
            'en' => "Sorry, I could not find that student ID. Please check it and try again, or contact the school office.",
            'hinglish' => "Maaf kijiye, yeh student ID mujhe nahi mila. Kripya check karke dobara bhejiye, ya school office se sampark karein.",
            'hi' => "क्षमा करें, यह स्टूडेंट आईडी नहीं मिली। कृपया जाँचकर दोबारा भेजें, या स्कूल ऑफिस से संपर्क करें।",
        ],
        'id_mismatch' => [
            'en' => "That student ID is not linked to this phone number, so I cannot share details. Please contact the school office to update your registered number.",
            'hinglish' => "Yeh student ID is phone number se juda hua nahi hai, isliye main jaankari nahi de sakta. Registered number update karane ke liye school office se sampark karein.",
            'hi' => "यह स्टूडेंट आईडी इस फोन नंबर से जुड़ी नहीं है, इसलिए मैं जानकारी नहीं दे सकता। रजिस्टर्ड नंबर अपडेट कराने के लिए स्कूल ऑफिस से संपर्क करें।",
        ],
        'escalated' => [
            'en' => "Thank you for telling us. I have passed your message to the school owner, who will personally get back to you soon. I'm not able to answer this one myself.",
            'hinglish' => "Humein batane ke liye dhanyavaad. Maine aapka message school owner ko bhej diya hai, woh jaldi aapse khud sampark karenge. Is vishay par main khud jawab nahi de sakta.",
            'hi' => "हमें बताने के लिए धन्यवाद। मैंने आपका संदेश स्कूल के मालिक को भेज दिया है, वे जल्द ही आपसे स्वयं संपर्क करेंगे। इस विषय पर मैं खुद उत्तर नहीं दे सकता।",
        ],
        'need_text' => [
            'en' => "I can't open voice notes or images here. Please type your question as text and I will help right away.",
            'hinglish' => "Main yahan voice note ya image nahi khol sakta. Kripya apna sawaal text mein likhkar bhejiye, main turant madad karunga.",
            'hi' => "मैं यहाँ वॉइस नोट या इमेज नहीं खोल सकता। कृपया अपना सवाल टेक्स्ट में लिखकर भेजें, मैं तुरंत मदद करूँगा।",
        ],
        'system_issue' => [
            'en' => "Sorry, we are facing a system issue right now. Please try again in a little while, or contact the school office.",
            'hinglish' => "Maaf kijiye, abhi system mein samasya aa rahi hai. Kripya thodi der baad dobara try karein, ya school office se sampark karein.",
            'hi' => "क्षमा करें, अभी सिस्टम में समस्या आ रही है। कृपया थोड़ी देर बाद फिर कोशिश करें, या स्कूल ऑफिस से संपर्क करें।",
        ],

        // ---- attendance -----------------------------------------------------
        'att_none' => [
            'en' => "No attendance has been recorded yet for %s.",
            'hinglish' => "%s ki attendance abhi tak record nahi hui hai.",
            'hi' => "%s की उपस्थिति अभी तक दर्ज नहीं हुई है।",
        ],
        'att_head' => [
            'en' => "Attendance for %s: overall %s, last 30 days %s.",
            'hinglish' => "%s ki attendance: overall %s, pichle 30 din mein %s.",
            'hi' => "%s की उपस्थिति: कुल %s, पिछले 30 दिनों में %s।",
        ],
        'att_absent_dates' => [
            'en' => "Recent absent dates: %s.",
            'hinglish' => "Haal ki absent tarikhein: %s.",
            'hi' => "हाल की अनुपस्थित तारीखें: %s।",
        ],
        'att_no_absent' => [
            'en' => "No absences recorded.",
            'hinglish' => "Koi absent record nahi hai.",
            'hi' => "कोई अनुपस्थिति दर्ज नहीं है।",
        ],
        'att_streak' => [
            'en' => "Current absence streak: %d day(s) in a row.",
            'hinglish' => "Abhi lagataar absent: %d din.",
            'hi' => "अभी लगातार अनुपस्थित: %d दिन।",
        ],
        'att_no_streak' => [
            'en' => "No current absence streak.",
            'hinglish' => "Abhi koi lagataar absence nahi hai.",
            'hi' => "अभी कोई लगातार अनुपस्थिति नहीं है।",
        ],

        // ---- marks ----------------------------------------------------------
        'marks_none' => [
            'en' => "No test marks have been recorded yet for %s.",
            'hinglish' => "%s ke test marks abhi tak record nahi hue hain.",
            'hi' => "%s के टेस्ट अंक अभी तक दर्ज नहीं हुए हैं।",
        ],
        'marks_head' => [
            'en' => "Test marks for %s:",
            'hinglish' => "%s ke test marks:",
            'hi' => "%s के टेस्ट अंक:",
        ],
        'marks_line' => [
            'en' => "%s: average %s, last score %s (%s). %s",
            'hinglish' => "%s: average %s, aakhri score %s (%s). %s",
            'hi' => "%s: औसत %s, आखिरी स्कोर %s (%s)। %s",
        ],
        'marks_change' => [
            'en' => "Change from the previous test: %s points, so it is %s.",
            'hinglish' => "Pichle test se badlav: %s points, yaani %s.",
            'hi' => "पिछले टेस्ट से बदलाव: %s अंक, यानी %s।",
        ],
        'marks_single' => [
            'en' => "Only one test so far, so no trend yet.",
            'hinglish' => "Abhi tak sirf ek test hua hai, isliye trend abhi nahi hai.",
            'hi' => "अभी तक सिर्फ एक टेस्ट हुआ है, इसलिए ट्रेंड अभी नहीं है।",
        ],
        'tr_improving' => ['en' => 'improving', 'hinglish' => 'behtar ho raha hai', 'hi' => 'सुधर रहा है'],
        'tr_declining' => ['en' => 'declining', 'hinglish' => 'gir raha hai', 'hi' => 'गिर रहा है'],
        'tr_stable'    => ['en' => 'stable', 'hinglish' => 'lagbhag sthir hai', 'hi' => 'लगभग स्थिर है'],

        // ---- strongest / weakest -------------------------------------------
        'str_both' => [
            'en' => "%s: strongest subject is %s (average %s) and weakest subject is %s (average %s).",
            'hinglish' => "%s: sabse mazboot subject %s hai (average %s) aur sabse kamzor subject %s hai (average %s).",
            'hi' => "%s: सबसे मजबूत विषय %s है (औसत %s) और सबसे कमजोर विषय %s है (औसत %s)।",
        ],
        'str_one' => [
            'en' => "%s: only %s has marks recorded so far (average %s), so strongest and weakest cannot be compared yet.",
            'hinglish' => "%s: abhi sirf %s ke marks record hue hain (average %s), isliye mazboot aur kamzor subject ki tulna abhi nahi ho sakti.",
            'hi' => "%s: अभी सिर्फ %s के अंक दर्ज हैं (औसत %s), इसलिए सबसे मजबूत और कमजोर विषय की तुलना अभी नहीं हो सकती।",
        ],

        // ---- fees -----------------------------------------------------------
        'fees_none' => [
            'en' => "No fee records found for %s.",
            'hinglish' => "%s ke liye koi fee record nahi mila.",
            'hi' => "%s के लिए कोई फीस रिकॉर्ड नहीं मिला।",
        ],
        'fees_head' => [
            'en' => "Fees for %s: total %s, paid %s, due %s.",
            'hinglish' => "%s ki fees: total %s, jama %s, baaki %s.",
            'hi' => "%s की फीस: कुल %s, जमा %s, बाकी %s।",
        ],
        'fees_due_date' => [
            'en' => "Due date: %s.",
            'hinglish' => "Antim tareekh: %s.",
            'hi' => "अंतिम तारीख: %s।",
        ],
        'fees_overdue' => [
            'en' => "This payment is overdue.",
            'hinglish' => "Yeh bhugtaan late ho chuka hai.",
            'hi' => "यह भुगतान देर से है।",
        ],
        'fees_clear' => [
            'en' => "No fee is due. Thank you!",
            'hinglish' => "Koi fee baaki nahi hai. Dhanyavaad!",
            'hi' => "कोई फीस बाकी नहीं है। धन्यवाद!",
        ],

        // ---- remarks --------------------------------------------------------
        'remarks_none' => [
            'en' => "No teacher remarks have been recorded yet for %s.",
            'hinglish' => "%s ke liye abhi tak koi teacher remark record nahi hua hai.",
            'hi' => "%s के लिए अभी तक कोई शिक्षक टिप्पणी दर्ज नहीं हुई है।",
        ],
        'remarks_head' => [
            'en' => "Last %d teacher remark(s) for %s:",
            'hinglish' => "%s ke aakhri %d teacher remark(s):",
            'hi' => "%s की आखिरी %d शिक्षक टिप्पणी:",
        ],

        // ---- full report ----------------------------------------------------
        'report_title' => [
            'en' => "Full report for %s (%s)",
            'hinglish' => "%s ki poori report (%s)",
            'hi' => "%s की पूरी रिपोर्ट (%s)",
        ],
        'report_good' => ['en' => 'Good news:', 'hinglish' => 'Achhi khabar:', 'hi' => 'अच्छी खबर:'],
        'report_attn' => ['en' => 'Needs attention:', 'hinglish' => 'Dhyaan dene wali baatein:', 'hi' => 'ध्यान देने योग्य बातें:'],
        'report_sugg' => ['en' => 'Suggestion:', 'hinglish' => 'Mera suggestion:', 'hi' => 'मेरा सुझाव:'],

        'good_att' => [
            'en' => "Attendance is strong at %s.",
            'hinglish' => "Attendance bahut achhi hai, %s.",
            'hi' => "उपस्थिति बहुत अच्छी है, %s।",
        ],
        'good_no_absent30' => [
            'en' => "No absences in the last 30 days.",
            'hinglish' => "Pichle 30 din mein ek bhi absent nahi.",
            'hi' => "पिछले 30 दिनों में एक भी अनुपस्थिति नहीं।",
        ],
        'good_improving' => [
            'en' => "%s is improving (%s points up in the last test).",
            'hinglish' => "%s mein sudhaar ho raha hai (pichle test mein %s points upar).",
            'hi' => "%s में सुधार हो रहा है (पिछले टेस्ट में %s अंक ऊपर)।",
        ],
        'good_strongest' => [
            'en' => "%s is the strongest subject (average %s).",
            'hinglish' => "%s sabse mazboot subject hai (average %s).",
            'hi' => "%s सबसे मजबूत विषय है (औसत %s)।",
        ],
        'good_fees' => [
            'en' => "Fees are fully paid.",
            'hinglish' => "Fees poori jama hai.",
            'hi' => "फीस पूरी जमा है।",
        ],
        'good_none' => [
            'en' => "Records are still building up, so there is no highlight yet.",
            'hinglish' => "Records abhi ban rahe hain, isliye abhi koi khaas baat nahi hai.",
            'hi' => "रिकॉर्ड अभी बन रहे हैं, इसलिए अभी कोई खास बात नहीं है।",
        ],

        'attn_att' => [
            'en' => "Overall attendance is %s, below the expected %s.",
            'hinglish' => "Overall attendance %s hai, jo expected %s se kam hai.",
            'hi' => "कुल उपस्थिति %s है, जो अपेक्षित %s से कम है।",
        ],
        'attn_last30' => [
            'en' => "Attendance in the last 30 days is only %s.",
            'hinglish' => "Pichle 30 din ki attendance sirf %s hai.",
            'hi' => "पिछले 30 दिनों की उपस्थिति सिर्फ %s है।",
        ],
        'attn_streak' => [
            'en' => "Currently absent for %d day(s) in a row.",
            'hinglish' => "Abhi lagataar %d din se absent hai.",
            'hi' => "अभी लगातार %d दिन से अनुपस्थित है।",
        ],
        'attn_declining' => [
            'en' => "%s is declining (%s points in the last test).",
            'hinglish' => "%s mein gir raha hai (pichle test mein %s points).",
            'hi' => "%s में गिरावट है (पिछले टेस्ट में %s अंक)।",
        ],
        'attn_weakest' => [
            'en' => "%s is the weakest subject (average %s).",
            'hinglish' => "%s sabse kamzor subject hai (average %s).",
            'hi' => "%s सबसे कमजोर विषय है (औसत %s)।",
        ],
        'attn_fee' => [
            'en' => "Fee due: %s%s.",
            'hinglish' => "Fees baaki: %s%s.",
            'hi' => "फीस बाकी: %s%s।",
        ],
        'attn_fee_by' => ['en' => ', due by %s', 'hinglish' => ', antim tareekh %s', 'hi' => ', अंतिम तारीख %s'],
        'attn_fee_overdue' => ['en' => ' (overdue)', 'hinglish' => ' (late ho chuki hai)', 'hi' => ' (देर हो चुकी है)'],
        'attn_none' => [
            'en' => "Nothing urgent right now.",
            'hinglish' => "Abhi kuch zaroori nahi hai.",
            'hi' => "अभी कुछ जरूरी नहीं है।",
        ],

        'sugg_streak' => [
            'en' => "Please find out why %s has been absent and send them back to school, or speak to the class teacher.",
            'hinglish' => "Kripya pata kijiye ki %s kyun absent hai aur use school bhejiye, ya class teacher se baat kijiye.",
            'hi' => "कृपया पता कीजिए कि %s क्यों अनुपस्थित है और उसे स्कूल भेजिए, या क्लास टीचर से बात कीजिए।",
        ],
        'sugg_att' => [
            'en' => "Please make sure %s attends school regularly - regular attendance helps marks too.",
            'hinglish' => "Kripya dhyaan dein ki %s school regularly aaye - regular attendance se marks bhi behtar hote hain.",
            'hi' => "कृपया ध्यान दें कि %s नियमित रूप से स्कूल आए - नियमित उपस्थिति से अंक भी बेहतर होते हैं।",
        ],
        'sugg_declining' => [
            'en' => "Please set aside 20 to 30 minutes of daily revision for %s and ask the teacher for extra practice.",
            'hinglish' => "Kripya %s ke liye roz 20 se 30 minute revision ka samay rakhiye aur teacher se extra practice maangiye.",
            'hi' => "कृपया %s के लिए रोज 20 से 30 मिनट दोहराने का समय रखिए और शिक्षक से अतिरिक्त अभ्यास माँगिए।",
        ],
        'sugg_weakest' => [
            'en' => "Please give %s about 20 minutes of extra practice daily; the teacher can suggest worksheets.",
            'hinglish' => "Kripya %s ko roz lagbhag 20 minute extra practice karaiye; teacher worksheets suggest kar sakte hain.",
            'hi' => "कृपया %s को रोज लगभग 20 मिनट अतिरिक्त अभ्यास कराइए; शिक्षक वर्कशीट सुझा सकते हैं।",
        ],
        'sugg_fee' => [
            'en' => "Please clear the pending fee by the due date to avoid any fine.",
            'hinglish' => "Kripya baaki fees antim tareekh tak jama kar dein taaki koi fine na lage.",
            'hi' => "कृपया बाकी फीस अंतिम तारीख तक जमा कर दें ताकि कोई जुर्माना न लगे।",
        ],
        'sugg_keep' => [
            'en' => "Keep up the good work - praise %s and keep the daily study routine going.",
            'hinglish' => "Aise hi lage rahiye - %s ki tareef kijiye aur roz padhai ki aadat banaye rakhiye.",
            'hi' => "ऐसे ही लगे रहिए - %s की तारीफ कीजिए और रोज पढ़ाई की आदत बनाए रखिए।",
        ],
    ];

    public const ATT_MIN = 75;      // expected attendance %
    public const WEAK_BELOW = 50;   // subject average considered weak %

    public static function t(string $key, string $lang, ...$args): string {
        $row = self::T[$key] ?? null;
        if (!$row) return $key;
        $tpl = $row[$lang] ?? $row['en'];
        return $args ? vsprintf($tpl, $args) : $tpl;
    }

    // ------------------------------------------------------------------
    // fixed messages
    public static function greeting(string $lang, string $school, array $names): string {
        return self::t('greeting', $lang, $school, self::joinNames($names, $lang));
    }
    public static function help(string $lang, array $names): string {
        return self::t('help', $lang, self::joinNames($names, $lang));
    }
    public static function askChild(string $lang, array $profiles): string {
        $lines = [];
        foreach ($profiles as $i => $p) {
            $lines[] = ($i + 1) . '. ' . $p['name'] . ($p['class'] ? ' (' . $p['class'] . ')' : '');
        }
        return self::t('ask_child', $lang, implode("\n", $lines));
    }
    public static function simple(string $key, string $lang, ...$args): string {
        return self::t($key, $lang, ...$args);
    }

    // ------------------------------------------------------------------
    /** Answer for one child. $topics: attendance|marks|strength|fees|remarks|report */
    public static function answer(array $topics, array $facts, string $lang): string {
        $name = $facts['student']['name'];
        if (in_array('report', $topics, true)) return self::report($facts, $lang);

        $order = ['attendance', 'marks', 'strength', 'fees', 'remarks'];
        $parts = [];
        foreach ($order as $topic) {
            if (!in_array($topic, $topics, true)) continue;
            $parts[] = match ($topic) {
                'attendance' => self::attendance($facts['attendance'], $name, $lang),
                'marks'      => self::marks($facts['marks'], $name, $lang),
                'strength'   => self::strength($facts['marks'], $name, $lang),
                'fees'       => self::fees($facts['fees'], $name, $lang),
                'remarks'    => self::remarks($facts['remarks'], $name, $lang),
            };
        }
        return implode("\n\n", $parts);
    }

    public static function attendance(array $a, string $name, string $lang): string {
        if (!$a['days_recorded']) return self::t('att_none', $lang, $name);
        $out = [self::t('att_head', $lang, $name, self::pct($a['overall_percent']), self::pct($a['last30_percent']))];
        $out[] = $a['recent_absent'] ? self::t('att_absent_dates', $lang, implode(', ', $a['recent_absent'])) : self::t('att_no_absent', $lang);
        $out[] = $a['absent_streak'] > 0 ? self::t('att_streak', $lang, $a['absent_streak']) : self::t('att_no_streak', $lang);
        return implode("\n", $out);
    }

    public static function marks(array $m, string $name, string $lang): string {
        if (!$m['subjects']) return self::t('marks_none', $lang, $name);
        $lines = [self::t('marks_head', $lang, $name)];
        foreach ($m['subjects'] as $s) {
            $tail = $s['change'] === null
                ? self::t('marks_single', $lang)
                : self::t('marks_change', $lang, self::signed($s['change']), self::t('tr_' . $s['trend'], $lang));
            $lines[] = self::t('marks_line', $lang, $s['subject'], self::num($s['average']) . '%', $s['last_score'], self::num($s['last_percent']) . '%', $tail);
        }
        return implode("\n", $lines);
    }

    public static function strength(array $m, string $name, string $lang): string {
        if (!$m['subjects']) return self::t('marks_none', $lang, $name);
        if ($m['only_one_subject']) {
            return self::t('str_one', $lang, $name, $m['strongest']['subject'], self::num($m['strongest']['average']) . '%');
        }
        return self::t('str_both', $lang, $name,
            $m['strongest']['subject'], self::num($m['strongest']['average']) . '%',
            $m['weakest']['subject'], self::num($m['weakest']['average']) . '%');
    }

    public static function fees(array $f, string $name, string $lang): string {
        if (!$f['has_fee_records']) return self::t('fees_none', $lang, $name);
        $c = $f['currency'];
        $out = [self::t('fees_head', $lang, $name, $c . self::money($f['total']), $c . self::money($f['paid']), $c . self::money($f['due']))];
        if ($f['due'] > 0) {
            if ($f['due_date']) $out[] = self::t('fees_due_date', $lang, $f['due_date']);
            if ($f['overdue']) $out[] = self::t('fees_overdue', $lang);
        } else {
            $out[] = self::t('fees_clear', $lang);
        }
        return implode(' ', array_slice($out, 0, 1)) . (count($out) > 1 ? "\n" . implode(' ', array_slice($out, 1)) : '');
    }

    public static function remarks(array $r, string $name, string $lang): string {
        if (!$r) return self::t('remarks_none', $lang, $name);
        $head = $lang === 'en' ? self::t('remarks_head', $lang, count($r), $name)
            : ($lang === 'hi' ? self::t('remarks_head', $lang, $name, count($r)) : self::t('remarks_head', $lang, $name, count($r)));
        $lines = [$head];
        foreach ($r as $i => $x) {
            $lines[] = ($i + 1) . '. ' . $x['text'] . ' (' . $x['context'] . ', ' . $x['date'] . ')';
        }
        return implode("\n", $lines);
    }

    // ------------------------------------------------------------------
    // Full report: good news -> needs attention -> one suggestion
    public static function plan(array $facts): array {
        $a = $facts['attendance']; $m = $facts['marks']; $f = $facts['fees'];
        $good = []; $attn = [];

        if ($a['days_recorded']) {
            if ($a['overall_percent'] >= 90) $good[] = ['good_att', [self::pct($a['overall_percent'])]];
            if ($a['last30_days'] > 0 && $a['last30_percent'] >= 100) $good[] = ['good_no_absent30', []];
            if ($a['overall_percent'] < self::ATT_MIN) $attn[] = ['attn_att', [self::pct($a['overall_percent']), self::ATT_MIN . '%']];
            if ($a['last30_days'] >= 5 && $a['last30_percent'] < self::ATT_MIN && $a['overall_percent'] >= self::ATT_MIN) {
                $attn[] = ['attn_last30', [self::pct($a['last30_percent'])]];
            }
            if ($a['absent_streak'] >= 1) $attn[] = ['attn_streak', [$a['absent_streak']]];
        }

        $improving = array_values(array_filter($m['subjects'], fn ($s) => $s['trend'] === 'improving'));
        $declining = array_values(array_filter($m['subjects'], fn ($s) => $s['trend'] === 'declining'));
        usort($improving, fn ($x, $y) => $y['change'] <=> $x['change']);
        usort($declining, fn ($x, $y) => $x['change'] <=> $y['change']);
        foreach (array_slice($improving, 0, 2) as $s) $good[] = ['good_improving', [$s['subject'], self::signed($s['change'])]];
        if ($m['strongest'] && !$m['only_one_subject'] && $m['strongest']['average'] >= 60) {
            $good[] = ['good_strongest', [$m['strongest']['subject'], self::num($m['strongest']['average']) . '%']];
        }
        foreach (array_slice($declining, 0, 2) as $s) $attn[] = ['attn_declining', [$s['subject'], self::signed($s['change'])]];
        if ($m['weakest'] && !$m['only_one_subject'] && ($m['weakest']['average'] < self::WEAK_BELOW || $m['weakest']['average'] < 60)) {
            $attn[] = ['attn_weakest', [$m['weakest']['subject'], self::num($m['weakest']['average']) . '%']];
        }

        if ($f['has_fee_records']) {
            if ($f['due'] <= 0) $good[] = ['good_fees', []];
            else $attn[] = ['attn_fee', [$f['currency'] . self::money($f['due']), $f['due_date'], $f['overdue']]];
        }

        // exactly one suggestion, by priority
        $name = $facts['student']['name'];
        $sugg = ['sugg_keep', [$name]];
        if ($a['absent_streak'] >= 3) $sugg = ['sugg_streak', [$name]];
        elseif ($a['days_recorded'] && $a['overall_percent'] < self::ATT_MIN) $sugg = ['sugg_att', [$name]];
        elseif ($declining) $sugg = ['sugg_declining', [$name]];
        elseif ($m['weakest'] && !$m['only_one_subject'] && $m['weakest']['average'] < self::WEAK_BELOW) $sugg = ['sugg_weakest', [$name]];
        elseif ($f['has_fee_records'] && $f['due'] > 0) $sugg = ['sugg_fee', []];

        return ['good' => $good, 'attention' => $attn, 'suggestion' => $sugg];
    }

    public static function renderPlan(array $plan, array $facts, string $lang): array {
        $line = function (array $item) use ($lang) {
            [$key, $args] = $item;
            if ($key === 'attn_fee') {
                [$amount, $date, $overdue] = $args;
                $suffix = ($date ? self::t('attn_fee_by', $lang, $date) : '') . ($overdue ? self::t('attn_fee_overdue', $lang) : '');
                return self::t('attn_fee', $lang, $amount, $suffix);
            }
            return self::t($key, $lang, ...$args);
        };
        return [
            'good'       => $plan['good'] ? array_map($line, $plan['good']) : [self::t('good_none', $lang)],
            'attention'  => $plan['attention'] ? array_map($line, $plan['attention']) : [self::t('attn_none', $lang)],
            'suggestion' => $line($plan['suggestion']),
        ];
    }

    public static function report(array $facts, string $lang): string {
        $r = self::renderPlan(self::plan($facts), $facts, $lang);
        $p = $facts['student'];
        $out = [self::t('report_title', $lang, $p['name'], $p['class'] ?: '-')];
        $out[] = self::t('report_good', $lang) . "\n" . implode("\n", $r['good']);
        $out[] = self::t('report_attn', $lang) . "\n" . implode("\n", $r['attention']);
        $out[] = self::t('report_sugg', $lang) . "\n" . $r['suggestion'];
        return implode("\n\n", $out);
    }

    // ------------------------------------------------------------------
    private static function joinNames(array $names, string $lang): string {
        if (count($names) <= 1) return $names[0] ?? '';
        $and = ['en' => ' and ', 'hinglish' => ' aur ', 'hi' => ' और '][$lang] ?? ' and ';
        return implode(', ', array_slice($names, 0, -1)) . $and . end($names);
    }
    public static function pct($v): string { return $v === null ? '-' : self::num($v) . '%'; }
    public static function num($v): string { $s = number_format((float) $v, 1, '.', ''); return rtrim(rtrim($s, '0'), '.'); }
    public static function signed($v): string { return ($v > 0 ? '+' : '') . self::num($v); }
    public static function money($v): string { return number_format((float) $v, 0, '.', ','); }
}
