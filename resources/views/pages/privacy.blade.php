@extends('Layout.app')

@section('title', __('Privacy') . ' | اقتبس')
@section('content')
    {{-- what Iqtabis keeps about you and why; every line here matches what the code really does --}}
    <x-text-page :title="__('Privacy')">
        @if (app()->getLocale() === 'ar')
            <section>
                <h2>ما البيانات التي نحفظها؟</h2>
                <ul>
                    <li><strong>حسابك:</strong> اسمك وبريدك الإلكتروني وكلمة المرور. تُحفظ كلمة المرور <strong>مشفّرة</strong> (hashed)، فلا يستطيع أحد الاطلاع عليها، ولا نحن.</li>
                    <li><strong>صورتك:</strong> إن رفعت صورة شخصية.</li>
                    <li><strong>نشاطك:</strong> اقتباساتك وإعجاباتك والمحفوظة وتعليقاتك وبلاغاتك والإشعارات التي تصلك.</li>
                </ul>
            </section>
            <section>
                <h2>ما الذي يراه الآخرون؟</h2>
                <p>يرى الجميع اقتباساتك وتعليقاتك مع اسمك وصورتك وتاريخ النشر. أما بريدك الإلكتروني فلا يظهر لأحد، إلا للمشرف في لوحة التحكم.</p>
            </section>
            <section>
                <h2>ملفات تعريف الارتباط (الكوكيز)</h2>
                <p>نستخدم ملف جلسة فقط، لتبقى مسجّلًا دخولك ولنحفظ اللغة والوضع الليلي. لا توجد إعلانات ولا أدوات تتبّع، ولا نشارك بياناتك مع أي جهة.</p>
            </section>
            <section>
                <h2>حذف حسابك</h2>
                <p>يمكنك حذف حسابك من «حسابي» بإدخال كلمة المرور. وعندها يُحذف كل شيء: الحساب والصورة والاقتباسات والإعجابات والمحفوظة والتعليقات والبلاغات والإشعارات.</p>
            </section>
        @else
            <section>
                <h2>What we keep</h2>
                <ul>
                    <li><strong>Your account:</strong> your name, email and password. The password is stored <strong>hashed</strong>, so not even we can read it.</li>
                    <li><strong>Your picture:</strong> if you upload one.</li>
                    <li><strong>Your activity:</strong> your quotes, likes, saved quotes, comments, reports and the notifications you receive.</li>
                </ul>
            </section>
            <section>
                <h2>What others see</h2>
                <p>Everyone sees your quotes and comments with your name, picture and date. Your email is never shown, except to admins on the dashboard.</p>
            </section>
            <section>
                <h2>Cookies</h2>
                <p>Only a session cookie, to keep you logged in and remember your language and theme. No ads, no trackers, and we share your data with no one.</p>
            </section>
            <section>
                <h2>Deleting your account</h2>
                <p>From "My account" you can delete your account with your password. Everything goes with it: the account, picture, quotes, likes, saved quotes, comments, reports and notifications.</p>
            </section>
        @endif
    </x-text-page>
@endsection
