<?php

namespace App\Http\Controllers;

use App\Models\LangNews;
use App\Models\Language;
use App\Models\News;
use App\Models\NLLBCode;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class NewsController extends Controller
{
    //
   

    public function topicStory($language, $topic, $slug, $id)
    {
        $lang = Language::where('language_code', $language)->first();
        $lang_2_letter = NLLBCode::where('nllb_code', $language)->first();
        $lang_2_letter = $lang_2_letter->language_code;
        $topic = strtoupper($topic);
        if ($language == 'eng_Latn') {
            $news = News::where('id', $id)
                ->where('active', 1)
                ->where('language', $language)
                ->orderBy('published_at', 'desc')
                ->first();
            //$prev = $news->links();
            //get the previous news
            //  
            if ($topic == 'ALL NEWS') {
                $prev = News::where('id', '<', $id)
                    ->where('active', 1)
                    ->where('language', $language)
                    ->orderBy('id', 'desc')
                    ->first();
                $next = News::where('id', '>', $id)
                    ->where('active', 1)
                    ->where('language', $language)
                    ->orderBy('id', 'asc')
                    ->first();
            } else {

                $prev = News::where('id', '<', $id)
                    ->where('active', 1)
                    ->where('language', $language)
                    ->where('topic', $topic)
                    ->orderBy('id', 'desc')
                    ->first();
                $next = News::where('id', '>', $id)
                    ->where('active', 1)
                    ->where('language', $language)
                    ->where('topic', $topic)
                    ->orderBy('id', 'asc')
                    ->first();
            }

            //return $news . $prev . $next;
            $slugc = preg_split('/\_/', $language)[1] == 'Latn' ? Str::slug($news->title, '-') : preg_replace('/\s+/u', '-', trim($news->title));
            //return $slugc;
            if ($slug != $slugc) {
                return redirect()->route('topicStory', ['language' => $language, 'topic' => strtolower($topic), 'slug' => $slugc, 'id' => $id]);
            }

            return view('topic.story', ['news' => $news, 'lang' => $lang, 'prev' => $prev, 'next' => $next, 'topic' => $topic, 'lang_2_letter' => $lang_2_letter]);
        } else {
            $news = LangNews::where('id', $id)
                ->where('active', 1)
                ->where('language', $language)
                ->orderBy('published_at', 'desc')
                ->first();
            //$prev = $news->links();
            //get the previous news
            //  
            if ($topic == 'ALL NEWS') {
                $prev = LangNews::where('id', '<', $id)
                    ->where('active', 1)
                    ->where('language', $language)
                    ->orderBy('id', 'desc')
                    ->first();
                $next = LangNews::where('id', '>', $id)
                    ->where('active', 1)
                    ->where('language', $language)
                    ->orderBy('id', 'asc')
                    ->first();
            } else {

                $prev = LangNews::where('id', '<', $id)
                    ->where('active', 1)
                    ->where('language', $language)
                    ->where('topic', $topic)
                    ->orderBy('id', 'desc')
                    ->first();
                $next = LangNews::where('id', '>', $id)
                    ->where('active', 1)
                    ->where('language', $language)
                    ->where('topic', $topic)
                    ->orderBy('id', 'asc')
                    ->first();
            }
        }
        //return $news . $prev . $next;
        $slugc = preg_split('/\_/', $language)[1] == 'Latn' ? Str::slug($news->title, '-') : preg_replace('/\s+/u', '-', trim($news->title));
        //return $slugc;
        if ($slug != $slugc) {
            return redirect()->route('topicStory', ['language' => $language, 'topic' => strtolower($topic), 'slug' => $slugc, 'id' => $id]);
        }

        return view('topic.story', ['news' => $news, 'lang' => $lang, 'prev' => $prev, 'next' => $next, 'topic' => $topic, 'lang_2_letter' => $lang_2_letter]);
    }
    public function store(Request $request)
    {
        $news = News::create($request->all());
        return response()->json($news, 201);
    }
    public function langNews($language)
    {
        $topic = 'All News';
        $topic = strtoupper($topic);
        $colors = [
            'ALL NEWS' => 'is-success',
            'TOP NEWS' => 'is-primary',
            'WORLD' => 'is-link',
            'BUSINESS' => 'is-info',
            'TECHNOLOGY' => 'is-success',
            'SCIENCE' => 'is-warning',
            'HEALTH' => 'is-danger',
            'ENTERTAINMENT' => 'is-primary',
            'SPORTS' => 'is-link'
        ];

        $color = $colors[$topic];
        $lang = Language::where('language_code', $language)->first();
        if ($language == 'eng_Latn') {
            $news = News::where('active', 1)
                ->orderBy('id', 'desc')
                ->orderBy('published_at', 'desc')
                ->paginate(10);
            return view(
                'topic.lang_topic',
                [
                    'news' => $news,
                    'lang' => $lang,
                    'topic' => $topic,
                    'color' => $color,
                ]
            );
            //return $news;
        } else {
            $news = LangNews::where('language', $language)
                ->where('active', 1)
                ->orderBy('id', 'desc')
                ->orderBy('published_at', 'desc')
                ->paginate(10);
            return view(
                'topic.lang_topic',
                [
                    'news' => $news,
                    'lang' => $lang,
                    'topic' => $topic,
                    'color' => $color,
                ]
            );
        }
    }
    public function countryLanguage($language, $topic)
    {
        $lang = Language::where('language_code', $language)->first();
        if ($language == 'eng_Latn') {
            return News::where('topic', $topic)
                ->orderBy('published_at', 'desc')
                ->take(1)
                ->get();
        } else {
            return LangNews::where('language', $language)
                ->where('topic', $topic)
                ->orderBy('published_at', 'desc')
                ->take(1)
                ->paginate(1);
        }
    }
    public function langTopicId($language, $topic, $id)
    {
        $topic = strtoupper($topic);
        $lang = Language::where('language_code', $language)->first();
        if ($language == 'eng_Latn') {
            if ($topic == 'ALL NEWS') {
                $news = News::where('language', 'eng_Latn')
                    ->where('id', $id)
                    ->where('active', 1)
                    ->orderBy('published_at', 'desc')
                    ->paginate(1)
                    ->get();
            } else {
                $news = News::where('language', 'eng_Latn')
                    ->where('id', $id)
                    ->where('active', 1)
                    ->where('topic', $topic)
                    ->orderBy('published_at', 'desc')
                    ->paginate(1)
                    ->get();
            }
            return view('lang_news', ['news' => $news, 'lang' => $lang]);
        } else {
            if ($topic == 'ALL NEWS') {
                $news = LangNews::where('language', $language)
                    ->where('id', $id)
                    ->where('active', 1)
                    ->orderBy('published_at', 'desc')
                    ->paginate(1)
                    ->get();
            } else {
                $news = LangNews::where('language', $language)
                    ->where('id', $id)
                    ->where('active', 1)
                    ->where('topic', $topic)
                    ->orderBy('published_at', 'desc')
                    ->paginate(1)
                    ->get();
            }
            return view('lang_news', ['news' => $news, 'lang' => $lang]);
        }
    }
    public function langTopic($language, $topic)
    {

        $topic = strtoupper($topic);
        $colors = [
            'ALL NEWS' => 'is-success',
            'TOP NEWS' => 'is-primary',
            'WORLD' => 'is-link',
            'BUSINESS' => 'is-info',
            'TECHNOLOGY' => 'is-success',
            'SCIENCE' => 'is-warning',
            'HEALTH' => 'is-danger',
            'ENTERTAINMENT' => 'is-primary',
            'SPORTS' => 'is-link'
        ];
        $color = $colors[$topic];
        $lang = Language::where('language_code', $language)->first();
        if ($language == 'eng_Latn') {
            if ($topic == 'ALL NEWS') {

                $news = News::where('active', 1)
                    ->orderBy('id', 'desc')
                    ->orderBy('published_at', 'desc')
                    ->paginate(10);
            } else {

                $news = News::where('topic', $topic)
                    ->where('active', 1)
                    ->orderBy('id', 'desc')
                    ->orderBy('published_at', 'desc')
                    ->paginate(10);
            }
            return view(
                'topic.lang_topic',
                [
                    'news' => $news,
                    'lang' => $lang,
                    'topic' => $topic,
                    'color' => $color,
                ]
            );
            //return $news;
        } else {
            if ($topic == 'ALL NEWS') {

                $news = LangNews::where('active', 1)
                    ->where('language', $language)

                    ->orderBy('id', 'desc')
                    ->orderBy('published_at', 'desc')
                    ->paginate(10);
            } else {

                $news = LangNews::where('topic', $topic)
                    ->where('language', $language)

                    ->where('active', 1)
                    ->orderBy('id', 'desc')
                    ->orderBy('published_at', 'desc')
                    ->paginate(10);
            }
            return view(
                'topic.lang_topic',
                [
                    'news' => $news,
                    'lang' => $lang,
                    'topic' => $topic,
                    'color' => $color,
                ]
            );
        }
    }
}
