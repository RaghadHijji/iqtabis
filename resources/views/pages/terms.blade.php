@extends('Layout.app')

@section('title', __('Terms') . ' | اقتبس')
@section('content')
    {{-- the house rules; the numbers here come from the code (155 letters, 3 reports) --}}
    <x-text-page :title="__('Terms')">
        @if (app()->getLocale() === 'ar')
            <section>
                <h2>لمن منصة اقتبس؟</h2>
                <p>اقتبس مساحة لمشاركة الاقتباسات التي تعني لك شيئًا. التصفح متاح للجميع، أما النشر والإعجاب والتعليق فلأصحاب الحسابات الذين أكّدوا بريدهم الإلكتروني.</p>
            </section>
            <section>
                <h2>قواعد النشر</h2>
                <ul>
                    <li>يتكوّن الاقتباس من 3 إلى 155 حرفًا، والتعليق من 2 إلى 300 حرف.</li>
                    <li>اكتب اسم القائل الصحيح إن كنت تعرفه، ولا تنسب إلى أحد قولًا لم يقله.</li>
                    <li>يُمنع المحتوى السياسي والسبّ والإساءة، ويرفض الموقع الكلمات الممنوعة تلقائيًا.</li>
                    <li>تُمنع الإعلانات والرسائل المزعجة.</li>
                </ul>
            </section>
            <section>
                <h2>البلاغات والإشراف</h2>
                <p>يحق لأي مستخدم الإبلاغ عن اقتباس. والاقتباس الذي يتلقى <strong>3 بلاغات</strong> يُخفى عن الجميع حتى يراجعه المشرف، فيعيده أو يحذفه.</p>
            </section>
            <section>
                <h2>اقتباساتك</h2>
                <p>اقتباساتك ملك لك، تعدّلها وتحذفها متى شئت، ولا يستطيع غيرك تعديلها. أما الاقتباسات المشهورة فحقوقها لأصحابها، ونعرضها دائمًا مع اسم قائلها.</p>
            </section>
            <section>
                <h2>حسابك</h2>
                <p>حافظ على سرية كلمة المرور. بعد 5 محاولات دخول خاطئة يُقفل تسجيل الدخول دقيقة واحدة حمايةً للحساب. وإن نسيتها فيمكنك تغييرها من «نسيت كلمة المرور؟».</p>
            </section>
        @else
            <section>
                <h2>Who Iqtabis is for</h2>
                <p>Iqtabis is a place to share the quotes that mean something to you. Anyone can browse; sharing, liking and commenting need an account with a confirmed email.</p>
            </section>
            <section>
                <h2>Posting rules</h2>
                <ul>
                    <li>A quote is 3 to 155 letters, a comment 2 to 300.</li>
                    <li>Credit the right author when you know it, and never put words in someone's mouth.</li>
                    <li>No politics, insults or abuse. Blocked words are refused automatically.</li>
                    <li>No ads or spam.</li>
                </ul>
            </section>
            <section>
                <h2>Reports and moderation</h2>
                <p>Anyone can report a quote. A quote with <strong>3 reports</strong> is hidden from everyone until an admin reviews it, and the admin can bring it back or delete it.</p>
            </section>
            <section>
                <h2>Your quotes</h2>
                <p>Your quotes are yours: edit or delete them any time, and no one else can edit them. Famous quotes belong to their authors, and we always show them with the author's name.</p>
            </section>
            <section>
                <h2>Your account</h2>
                <p>Keep your password safe. After 5 wrong log-ins, logging in is locked for a minute to protect the account. If you forget it, use "Forgot password?".</p>
            </section>
        @endif
    </x-text-page>
@endsection
