<?php
namespace Library;
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

class TB{

    const BLOG_CATEGORY     = 'blog_category';
    const MEMBER            = 'member';
    const POST              = 'post';
    const USER              = 'user';
    const SLIDER            = 'slider';
    const LOCATION_SCHOOLS  = 'location_schools';
    const DISTRICT          = 'district';
    const WARD              = 'ward';
    const STREET            = 'street';
    const GROUP_VOCATIONS   = 'group_vocation';
    const QUESTION_VOCATIONS   = 'question_vocation';

    const F_BLOG_CATEGORY = [
        'id', 'name', 'slug', 'description', 'image', 'parent', 'title', 'keywords',
        'seo_desc', 'seo_h1', 'seo_h2', 'seo_h3', 'seo_h4', 'seo_h5', 'seo_h6',
        'created_at', 'created_by', 'updated_at', 'updated_by', 'ordering'
    ];

    const F_POST = [
        'id', 'name', 'slug', 'description', 'image', 'cate_id', 'content', 'title', 'keywords', 'seo_desc',
        'seo_h1', 'seo_h2', 'seo_h3', 'seo_h4', 'seo_h5', 'seo_h6', 'created_at', 'created_by', 'updated_at',
        'updated_by', 'home', 'popular'
    ];

    const F_MEMBER = [
        'id', 'fullname', 'password', 'email', 'phone', 'birthday', 'gender', 'current_class', 'created_at',
        'actived', 'token_active'
    ];

    const F_USER = [
        'id', 'username', 'email', 'password', 'firstname', 'lastname', 'gender'
    ];

    const F_SLIDER = [
        'id', 'image', 'title', 'description', 'alt', 'ordering', 'status'
    ];

    const F_LOCATION_SCHOOLS = [
        'id' , 'name', 'address', 'district_id', 'ward_id', 'street', 'point', 'lat', 'lng', 
        'created_at', 'created_by', 'updated_at', 'updated_by'
    ];

    const F_DISTRICT = ['id', 'name'];

    const F_WARD = ['id', 'name', 'district_id'];
    const F_STREET = ['id', 'name'];

    const F_GROUP_VOCATION = [
        'id', 'name', 'ordering'
    ];

    const F_QUESTION_VOCATION = [
        'id', 'group_id', 'question', 'point', 'ordering'
    ];
}