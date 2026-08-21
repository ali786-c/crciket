package com.devwithguru.cricket.data.db.dao

import androidx.room.Dao
import androidx.room.Delete
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import androidx.room.Update
import com.devwithguru.cricket.data.db.entity.FixtureEntity
import kotlinx.coroutines.flow.Flow

@Dao
interface FixtureDao {
    @Query("SELECT * FROM fixtures ORDER BY date DESC")
    fun getAllFixtures(): Flow<List<FixtureEntity>>

    @Query("SELECT * FROM fixtures WHERE id = :id")
    suspend fun findById(id: String): FixtureEntity?

    @Query("SELECT * FROM fixtures WHERE id = :id")
    fun observeById(id: String): Flow<FixtureEntity?>

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertFixture(fixture: FixtureEntity): Long

    @Update
    suspend fun updateFixture(fixture: FixtureEntity)

    @Delete
    suspend fun deleteFixture(fixture: FixtureEntity)

    @Query("UPDATE fixtures SET status = :status WHERE id = :id")
    suspend fun updateStatus(id: String, status: String)

    @Query("SELECT * FROM fixtures WHERE status = :status ORDER BY date DESC")
    fun getFixturesByStatus(status: String): Flow<List<FixtureEntity>>
}
